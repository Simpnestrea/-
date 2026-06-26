<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Recipe;

class CommentController extends Controller
{
    /**
     * Lưu bình luận mới cho món ăn.
     */
    public function store(Request $request, Recipe $recipe)
    {
        // Xác thực dữ liệu đầu vào
        $validatedData = $request->validate([
            'content' => 'required|string|max:1000',
        ], [
            'content.required' => 'Nội dung bình luận không được để trống.',
            'content.string' => 'Nội dung bình luận không hợp lệ.',
            'content.max' => 'Nội dung bình luận không được vượt quá 1000 ký tự.',
        ]);

        // Tạo bình luận liên kết với món ăn và người dùng đang đăng nhập
        $comment = $recipe->comments()->create([
            'user_id' => auth()->id(),
            'content' => $validatedData['content'],
        ]);

        // OBSERVER PATTERN: Gắn (Attach) tác giả vào danh sách theo dõi và thông báo (notify)
        if ($recipe->user_id !== auth()->id()) { // Không thông báo nếu tự bình luận vào bài của mình
            $authorObserver = new \App\Observers\UserAuthor($recipe->user);
            $recipe->attach($authorObserver);
            $recipe->comment($comment);
        }

        // Quay lại trang trước kèm theo thông báo thành công (cuộn tới phần bình luận)
        return redirect()->to(url()->previous() . '#comments-section')->with('success', 'Bình luận của bạn đã được đăng thành công!');
    }

    /**
     * Thích hoặc không thích bình luận.
     */
    public function toggleReaction(Request $request, Comment $comment)
    {
        $request->validate([
            'type' => 'required|in:like,dislike'
        ]);

        $type = $request->input('type');
        $userId = auth()->id();

        $existing = $comment->reactions()->where('user_id', $userId)->first();

        if ($existing) {
            if ($existing->type === $type) {
                // Hủy phản hồi nếu nhấn lại cùng nút
                $existing->delete();
                $message = $type === 'like' ? 'Đã bỏ thích bình luận!' : 'Đã bỏ không thích bình luận!';
            } else {
                // Đổi loại phản hồi
                $existing->update(['type' => $type]);
                $message = $type === 'like' ? 'Đã thích bình luận!' : 'Đã không thích bình luận!';
            }
        } else {
            // Tạo tương tác mới
            $comment->reactions()->create([
                'user_id' => $userId,
                'type' => $type
            ]);
            $message = $type === 'like' ? 'Đã thích bình luận!' : 'Đã không thích bình luận!';
        }

        if ($request->wantsJson()) {
            $likesCount = $comment->reactions()->where('type', 'like')->count();
            $dislikesCount = $comment->reactions()->where('type', 'dislike')->count();
            $userReaction = $comment->reactions()->where('user_id', $userId)->first();

            return response()->json([
                'success' => true,
                'message' => $message,
                'likes_count' => $likesCount,
                'dislikes_count' => $dislikesCount,
                'user_reaction' => $userReaction ? $userReaction->type : null,
            ]);
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Gửi tố cáo bình luận vi phạm.
     */
    public function report(Request $request, Comment $comment)
    {
        $request->validate([
            'reason' => 'required|string|max:255'
        ], [
            'reason.required' => 'Vui lòng chọn hoặc nhập lý do tố cáo.',
            'reason.string' => 'Lý do tố cáo không hợp lệ.',
            'reason.max' => 'Lý do tố cáo không được vượt quá 255 ký tự.'
        ]);

        $reason = $request->input('reason');
        if ($reason === 'Khác') {
            $request->validate([
                'custom_reason' => 'required|string|max:255'
            ], [
                'custom_reason.required' => 'Vui lòng nhập lý do cụ thể.'
            ]);
            $reason = 'Khác: ' . $request->input('custom_reason');
        }

        // Tránh gửi tố cáo nhiều lần từ cùng một tài khoản
        $existing = $comment->reports()->where('user_id', auth()->id())->exists();
        if ($existing) {
            return redirect()->to(url()->previous() . '#comments-section')->with('error', 'Bạn đã gửi báo cáo tố cáo bình luận này trước đó.');
        }

        $comment->reports()->create([
            'user_id' => auth()->id(),
            'reason' => $reason
        ]);

        return redirect()->to(url()->previous() . '#comments-section')->with('success', 'Tố cáo bình luận thành công! Ban quản trị sẽ xử lý sớm nhất.');
    }

    /**
     * Cập nhật bình luận của chính mình.
     */
    public function update(Request $request, Comment $comment)
    {
        if ($comment->user_id !== auth()->id()) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Bạn không có quyền chỉnh sửa bình luận này.'], 403);
            }
            abort(403, 'Bạn không có quyền chỉnh sửa bình luận này.');
        }

        $validatedData = $request->validate([
            'content' => 'required|string|max:1000',
        ], [
            'content.required' => 'Nội dung bình luận không được để trống.',
            'content.string' => 'Nội dung bình luận không hợp lệ.',
            'content.max' => 'Nội dung bình luận không được vượt quá 1000 ký tự.',
        ]);

        $comment->update([
            'content' => $validatedData['content']
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật bình luận thành công!',
                'content' => $comment->content
            ]);
        }

        return redirect()->to(url()->previous() . '#comments-section')->with('success', 'Chỉnh sửa bình luận thành công!');
    }

    /**
     * Xóa bình luận của chính mình.
     */
    public function destroy(Request $request, Comment $comment)
    {
        if ($comment->user_id !== auth()->id()) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Bạn không có quyền xóa bình luận này.'], 403);
            }
            abort(403, 'Bạn không có quyền xóa bình luận này.');
        }

        $comment->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Xóa bình luận thành công!'
            ]);
        }

        return redirect()->to(url()->previous() . '#comments-section')->with('success', 'Xóa bình luận thành công!');
    }
}
