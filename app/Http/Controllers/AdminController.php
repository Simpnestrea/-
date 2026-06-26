<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Recipe;
use App\Models\Category;
use App\Models\Comment;
use App\Models\CommentReport;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Dashboard Overview.
     */
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'premium_users' => User::where('is_premium', true)->count(),
            'admin_users' => User::where('is_admin', true)->count(),
            'total_recipes' => Recipe::count(),
            'total_comments' => Comment::count(),
            'total_reports' => CommentReport::count(),
            'total_categories' => Category::count(),
            'total_revenue' => DB::table('recipe_user_purchases')->sum('price') ?: 0,
            'total_wallet_balance' => User::sum('balance') ?: 0,
        ];

        // Món ngon nổi bật: 5 món có lượt xem nhiều nhất
        $topRecipes = Recipe::with('user', 'category')->orderBy('views_count', 'desc')->take(5)->get();

        // Hoạt động gần đây: 5 người dùng mới đăng ký
        $recentUsers = User::orderBy('id', 'desc')->take(5)->get();

        // 5 Báo cáo tố cáo gần đây
        $recentReports = CommentReport::with(['user', 'comment', 'comment.user', 'comment.recipe'])
            ->orderBy('id', 'desc')
            ->take(5)
            ->get();

        // DỮ LIỆU BIỂU ĐỒ (CHARTS DATA)
        // 1. Phân bổ danh mục công thức
        $categoriesData = Category::withCount('recipes')
            ->orderBy('recipes_count', 'desc')
            ->take(6)
            ->get()
            ->map(function ($cat) {
                return [
                    'name' => $cat->name,
                    'count' => $cat->recipes_count
                ];
            });

        // 2. Tỷ lệ Free vs Premium
        $userRatio = [
            'premium' => $stats['premium_users'],
            'free' => max(0, $stats['total_users'] - $stats['premium_users'])
        ];

        // 3. Biểu đồ đăng ký thành viên mới trong 15 ngày qua
        $days = [];
        $registrations = [];
        for ($i = 14; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $days[] = now()->subDays($i)->format('d/m');
            $registrations[$date] = 0;
        }

        $recentRegistrations = User::where('created_at', '>=', now()->subDays(15))
            ->get()
            ->groupBy(function($user) {
                return $user->created_at->format('Y-m-d');
            });

        foreach ($recentRegistrations as $date => $usersGroup) {
            if (isset($registrations[$date])) {
                $registrations[$date] = $usersGroup->count();
            }
        }

        $chartUserGrowth = [
            'labels' => $days,
            'data' => array_values($registrations)
        ];

        // 4. Biểu đồ độ khó món ăn
        $difficultyData = [
            'easy' => Recipe::where('difficulty', 'dễ')->count(),
            'medium' => Recipe::where('difficulty', 'trung bình')->count(),
            'hard' => Recipe::where('difficulty', 'khó')->count(),
        ];

        // 5. Thống kê cấp bậc ẩm thực
        $roleStats = [
            'beginner' => User::where('role', 'beginner')->count(),
            'homecook' => User::where('role', 'homecook')->count(),
            'prochef' => User::where('role', 'prochef')->count(),
            'masterchef' => User::where('role', 'masterchef')->count(),
        ];

        return view('admin.dashboard', compact(
            'stats', 
            'topRecipes', 
            'recentUsers', 
            'recentReports',
            'categoriesData',
            'userRatio',
            'chartUserGrowth',
            'difficultyData',
            'roleStats'
        ));
    }

    /**
     * Users list & management.
     */
    public function users(Request $request)
    {
        $query = $request->input('query');
        $adminRole = $request->input('admin_role'); // 'admin', 'user'
        $role = $request->input('role'); // 'beginner', 'homecook', 'prochef', 'masterchef'
        $status = $request->input('status'); // 'premium', 'free'
        
        $usersQuery = User::withCount(['recipes', 'comments']);

        if ($query) {
            $usersQuery->where(function($q) use ($query) {
                $q->whereLikeWithoutAccents('name', "%{$query}%")
                  ->orWhereLikeWithoutAccents('username', "%{$query}%")
                  ->orWhereLikeWithoutAccents('email', "%{$query}%");
            });
        }

        if ($adminRole) {
            if ($adminRole === 'admin') {
                $usersQuery->where('is_admin', true);
            } elseif ($adminRole === 'user') {
                $usersQuery->where('is_admin', false);
            }
        }

        if ($role) {
            $usersQuery->where('role', $role);
        }

        if ($status) {
            if ($status === 'premium') {
                $usersQuery->where('is_premium', true);
            } elseif ($status === 'free') {
                $usersQuery->where('is_premium', false);
            }
        }

        $users = $usersQuery->orderBy('id', 'desc')->paginate(10);

        return view('admin.users', compact('users', 'query', 'adminRole', 'role', 'status'));
    }

    /**
     * Get user details (JSON).
     */
    public function userDetail(User $user)
    {
        $user->loadCount(['recipes', 'comments']);
        $recentRecipes = $user->recipes()->with('category')->orderBy('id', 'desc')->take(5)->get();
        $recentComments = $user->comments()->with('recipe')->orderBy('id', 'desc')->take(5)->get();
        
        return response()->json([
            'user' => $user,
            'recent_recipes' => $recentRecipes,
            'recent_comments' => $recentComments
        ]);
    }

    /**
     * Get recipe details (JSON).
     */
    public function recipeDetail(Recipe $recipe)
    {
        $recipe->load(['user', 'category', 'ingredients', 'steps' => function($q) {
            $q->orderBy('order');
        }]);
        
        return response()->json($recipe);
    }

    /**
     * Change user culinary role.
     */
    public function changeRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|string|in:beginner,homecook,prochef,masterchef'
        ]);

        $user->role = $request->role;
        $user->save();

        $message = "Đã thay đổi cấp bậc của {$user->name} thành " . $user->role_label . "!";

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'role' => $user->role,
                'role_label' => $user->role_label,
                'user_id' => $user->id
            ]);
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Toggle admin privilege.
     */
    public function toggleAdmin(Request $request, User $user)
    {
        if (auth()->id() === $user->id) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Bạn không thể tự tước quyền Admin của chính mình!'], 400);
            }
            return redirect()->back()->with('error', 'Bạn không thể tự tước quyền Admin của chính mình!');
        }

        $user->is_admin = !$user->is_admin;
        $user->save();

        $status = $user->is_admin ? 'thăng cấp làm Admin' : 'hạ cấp thành người dùng thường';
        $message = "Đã {$status} cho tài khoản {$user->name} thành công!";

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'is_admin' => $user->is_admin,
                'user_id' => $user->id
            ]);
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Toggle premium status.
     */
    public function togglePremium(Request $request, User $user)
    {
        $user->is_premium = !$user->is_premium;
        $user->save();

        $status = $user->is_premium ? 'kích hoạt gói Premium' : 'hủy gói Premium';
        $message = "Đã {$status} cho tài khoản {$user->name} thành công!";

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'is_premium' => $user->is_premium,
                'user_id' => $user->id
            ]);
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Delete user account.
     */
    public function deleteUser(Request $request, User $user)
    {
        if (auth()->id() === $user->id) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Bạn không thể tự xóa tài khoản của chính mình!'], 400);
            }
            return redirect()->back()->with('error', 'Bạn không thể tự xóa tài khoản của chính mình!');
        }

        $user->delete();
        $message = "Đã xóa tài khoản {$user->name} thành công!";

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'user_id' => $user->id
            ]);
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Recipes list & management.
     */
    public function recipes(Request $request)
    {
        $query = $request->input('query');
        $categoryId = $request->input('category_id');
        $difficulty = $request->input('difficulty');
        $premiumStatus = $request->input('premium_status'); // 'premium', 'free'

        $recipesQuery = Recipe::with(['user', 'category']);

        if ($query) {
            $recipesQuery->where(function($q) use ($query) {
                $q->whereLikeWithoutAccents('title', "%{$query}%")
                  ->orWhereLikeWithoutAccents('description', "%{$query}%")
                  ->orWhereHas('user', function($q2) use ($query) {
                      $q2->whereLikeWithoutAccents('name', "%{$query}%");
                  });
            });
        }

        if ($categoryId) {
            $recipesQuery->where('category_id', $categoryId);
        }

        if ($difficulty) {
            $recipesQuery->where('difficulty', $difficulty);
        }

        if ($premiumStatus) {
            if ($premiumStatus === 'premium') {
                $recipesQuery->where('is_premium', true);
            } elseif ($premiumStatus === 'free') {
                $recipesQuery->where('is_premium', false);
            }
        }

        $recipes = $recipesQuery->orderBy('id', 'desc')->paginate(10);
        $categories = Category::all();

        return view('admin.recipes', compact('recipes', 'categories', 'query', 'categoryId', 'difficulty', 'premiumStatus'));
    }

    /**
     * Delete recipe.
     */
    public function deleteRecipe(Request $request, Recipe $recipe)
    {
        $recipeId = $recipe->id;
        $recipe->delete();
        $message = "Đã xóa công thức nấu ăn \"{$recipe->title}\" thành công!";

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'recipe_id' => $recipeId
            ]);
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Publish recipe using State Pattern.
     */
    public function publishRecipe(Request $request, Recipe $recipe)
    {
        try {
            $context = new \App\States\Recipe\RecipeContext($recipe);
            $context->publish();
            
            $message = "Đã duyệt công thức nấu ăn \"{$recipe->title}\" thành công!";
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'status' => $context->getStatusName()
                ]);
            }
            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
            }
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Reject recipe using State Pattern.
     */
    public function rejectRecipe(Request $request, Recipe $recipe)
    {
        try {
            $context = new \App\States\Recipe\RecipeContext($recipe);
            $context->reject();
            
            $message = "Đã từ chối công thức nấu ăn \"{$recipe->title}\" thành công!";
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'status' => $context->getStatusName()
                ]);
            }
            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
            }
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Categories list & management.
     */
    public function categories()
    {
        $categories = Category::withCount('recipes')->orderBy('id', 'desc')->get();
        return view('admin.categories', compact('categories'));
    }

    /**
     * Store new category.
     */
    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'image_file' => 'nullable|image|max:2048',
            'image_url' => 'nullable|url',
        ], [
            'name.required' => 'Vui lòng nhập tên danh mục.',
            'name.unique' => 'Tên danh mục này đã tồn tại.',
            'image_file.image' => 'File tải lên phải là định dạng hình ảnh.',
            'image_file.max' => 'Ảnh đại diện không được vượt quá 2MB.',
            'image_url.url' => 'Địa chỉ liên kết ảnh không hợp lệ.',
        ]);

        $imageUrl = null;

        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('categories', 'public');
            $imageUrl = Storage::url($path);
        } elseif ($request->input('image_url')) {
            $imageUrl = $request->input('image_url');
        }

        $slug = Str::slug($request->name);
        $originalSlug = $slug;
        $count = 1;
        while (Category::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        Category::create([
            'name' => $request->name,
            'slug' => $slug,
            'image' => $imageUrl,
        ]);

        return redirect()->back()->with('success', 'Đã tạo danh mục mới thành công!');
    }

    /**
     * Update category.
     */
    public function updateCategory(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'image_file' => 'nullable|image|max:2048',
            'image_url' => 'nullable|url',
        ], [
            'name.required' => 'Vui lòng nhập tên danh mục.',
            'name.unique' => 'Tên danh mục này đã tồn tại.',
            'image_file.image' => 'File tải lên phải là định dạng hình ảnh.',
            'image_file.max' => 'Ảnh đại diện không được vượt quá 2MB.',
            'image_url.url' => 'Địa chỉ liên kết ảnh không hợp lệ.',
        ]);

        $imageUrl = $category->image;

        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('categories', 'public');
            $imageUrl = Storage::url($path);
        } elseif ($request->filled('image_url')) {
            $imageUrl = $request->input('image_url');
        }

        $slug = Str::slug($request->name);
        if ($slug !== $category->slug) {
            $originalSlug = $slug;
            $count = 1;
            while (Category::where('slug', $slug)->where('id', '!=', $category->id)->exists()) {
                $slug = $originalSlug . '-' . $count;
                $count++;
            }
        } else {
            $slug = $category->slug;
        }

        $category->update([
            'name' => $request->name,
            'slug' => $slug,
            'image' => $imageUrl,
        ]);

        return redirect()->back()->with('success', 'Đã cập nhật danh mục thành công!');
    }

    /**
     * Delete category.
     */
    public function deleteCategory(Category $category)
    {
        $category->delete();
        return redirect()->back()->with('success', 'Đã xóa danh mục thành công!');
    }

    public function reports(Request $request)
    {
        $query = $request->input('query');
        $reportsQuery = CommentReport::with(['user', 'comment', 'comment.user', 'comment.recipe']);

        if ($query) {
            $reportsQuery->where(function($q) use ($query) {
                $q->whereLikeWithoutAccents('reason', "%{$query}%")
                  ->orWhereHas('user', function($q2) use ($query) {
                      $q2->whereLikeWithoutAccents('name', "%{$query}%");
                  })
                  ->orWhereHas('comment', function($q3) use ($query) {
                      $q3->whereLikeWithoutAccents('content', "%{$query}%")
                         ->orWhereHas('user', function($q4) use ($query) {
                             $q4->whereLikeWithoutAccents('name', "%{$query}%");
                         });
                  });
            });
        }

        $reports = $reportsQuery->orderBy('id', 'desc')->paginate(15);
        return view('admin.reports', compact('reports', 'query'));
    }

    /**
     * Dismiss a comment report.
     */
    public function dismissReport(Request $request, CommentReport $report)
    {
        $reportId = $report->id;
        $report->delete();
        $message = 'Đã bỏ qua báo cáo tố cáo thành công!';

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'report_id' => $reportId
            ]);
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Delete the reported comment.
     */
    public function deleteReportedComment(Request $request, CommentReport $report)
    {
        $comment = $report->comment;
        $reportId = $report->id;
        if ($comment) {
            $comment->delete();
            $message = 'Đã xóa bình luận vi phạm thành công!';
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'report_id' => $reportId,
                    'comment_deleted' => true
                ]);
            }
            return redirect()->back()->with('success', 'Đã xóa bình luận vi phạm thành công (các tố cáo liên quan cũng đã tự động được xóa)!');
        }
        
        $report->delete();
        $message = 'Bình luận không còn tồn tại, đã xóa bản ghi báo cáo!';
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'report_id' => $reportId,
                'comment_deleted' => false
            ]);
        }
        return redirect()->back()->with('success', $message);
    }
}

