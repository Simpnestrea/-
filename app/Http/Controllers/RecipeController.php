<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Step;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class RecipeController extends Controller
{
    /**
     * Hiển thị form tạo công thức nấu ăn mới.
     */
    public function create()
    {
        $categories = Category::all();
        return view('recipe.create', compact('categories'));
    }

    /**
     * Lưu công thức nấu ăn mới vào cơ sở dữ liệu.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'time_to_cook' => ['required', 'integer', 'min:1'],
            'difficulty' => ['required', 'in:dễ,trung bình,khó'],
            'category_id' => ['required', 'exists:categories,id'],
            'image' => ['nullable', 'image', 'max:2048'],
            'tips' => ['nullable', 'string'],
            'ingredients' => ['required', 'array', 'min:1'],
            'ingredients.*.name' => ['required', 'string', 'max:255'],
            'ingredients.*.quantity' => ['nullable', 'string', 'max:255'],
            'ingredients.*.unit' => ['nullable', 'string', 'max:255'],
            'steps' => ['required', 'array', 'min:1'],
            'steps.*.content' => ['required', 'string'],
            'steps.*.image' => ['nullable', 'image', 'max:2048'],
        ], [
            'title.required' => 'Vui lòng nhập tên món ăn.',
            'time_to_cook.required' => 'Vui lòng nhập thời gian nấu.',
            'time_to_cook.integer' => 'Thời gian nấu phải là một số nguyên (phút).',
            'category_id.required' => 'Vui lòng chọn danh mục.',
            'category_id.exists' => 'Danh mục đã chọn không hợp lệ.',
            'image.image' => 'Ảnh đại diện món ăn phải là định dạng hình ảnh.',
            'image.max' => 'Ảnh đại diện món ăn không được vượt quá 2MB.',
            'ingredients.required' => 'Vui lòng nhập ít nhất một nguyên liệu.',
            'ingredients.*.name.required' => 'Vui lòng nhập tên nguyên liệu.',
            'steps.required' => 'Vui lòng nhập ít nhất một bước thực hiện.',
            'steps.*.content.required' => 'Vui lòng nhập nội dung cho bước thực hiện.',
            'steps.*.image.image' => 'Hình ảnh minh họa cho bước nấu phải là định dạng hình ảnh.',
            'steps.*.image.max' => 'Hình ảnh minh họa cho bước nấu không được vượt quá 2MB.',
        ]);

        try {
            DB::beginTransaction();

            // 1. Xử lý ảnh đại diện món ăn
            $imageUrl = null;
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('recipes', 'public');
                $imageUrl = Storage::url($path);
            }

            // Trích xuất hình ảnh của các bước nấu (nếu có) thành cấu trúc mảng
            $stepImages = [];
            if ($request->has('steps')) {
                foreach ($request->steps as $index => $stepData) {
                    $stepImages[$index] = ['image' => $request->file("steps.{$index}.image") ?? null];
                }
            }

            // BUILDER PATTERN: Khởi tạo Recipe thông qua Builder
            $builder = new \App\Builders\RecipeBuilder();
            
            $recipe = $builder->addBasicInfo($request->all(), $imageUrl)
                              ->addIngredients($request->ingredients)
                              ->addSteps($request->steps, $stepImages)
                              ->build();

            DB::commit();

            // Tự động kiểm tra nâng cấp cấp bậc
            $oldRole = auth()->user()->role;
            $newRole = auth()->user()->updateCulinaryRole();
            $upgradeMessage = '';
            if ($newRole !== $oldRole) {
                $upgradeMessage = ' Chúc mừng bạn đã thăng cấp thành ' . auth()->user()->role_label . '!';
            }

            return redirect()->route('recipe.detail', $recipe->slug)
                             ->with('success', 'Đăng công thức thành công!' . $upgradeMessage);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Đã có lỗi xảy ra khi lưu công thức: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Lưu hoặc bỏ lưu công thức nấu ăn.
     */
    public function toggleSave(Recipe $recipe)
    {
        $user = auth()->user();
        $isSaved = false;
        if ($user->savedRecipes()->where('recipe_id', $recipe->id)->exists()) {
            $user->savedRecipes()->detach($recipe->id);
            $message = 'Đã hủy lưu món ăn khỏi thư viện!';
        } else {
            $user->savedRecipes()->attach($recipe->id);
            $message = 'Đã lưu món ăn vào thư viện!';
            $isSaved = true;
        }

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'is_saved' => $isSaved,
            ]);
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Thích hoặc bỏ thích công thức nấu ăn.
     */
    public function toggleLike(Recipe $recipe)
    {
        $user = auth()->user();
        $isLiked = false;

        if ($user->likedRecipes()->where('recipe_id', $recipe->id)->exists()) {
            $user->likedRecipes()->detach($recipe->id);
            $message = 'Đã bỏ thích món ăn!';
        } else {
            $user->likedRecipes()->attach($recipe->id);
            $message = 'Đã thích món ăn!';
            $isLiked = true;

            // OBSERVER PATTERN: Gắn (Attach) tác giả vào danh sách theo dõi và thông báo (notify)
            if ($recipe->user_id !== $user->id) { // Không thông báo nếu tự like bài của mình
                $authorObserver = new \App\Observers\UserAuthor($recipe->user);
                $recipe->attach($authorObserver);
                $recipe->like($user);
            }
        }

        // Tự động kiểm tra nâng cấp cấp bậc của tác giả công thức
        if ($recipe->user) {
            $recipe->user->updateCulinaryRole();
        }

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'is_liked' => $isLiked,
                'likes_count' => $recipe->likedByUsers()->count(),
            ]);
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Hiển thị kho món ngon của người dùng (Bếp của bạn).
     */
    public function myKitchen()
    {
        $user = auth()->user();
        
        // Công thức đã viết (Món của tôi)
        $myRecipes = $user->recipes()->with('category')->latest()->get();
        
        // Công thức đã lưu
        $savedRecipes = $user->savedRecipes()->with(['user', 'category'])->latest()->get();
        
        // Tính toán các số liệu thống kê
        $myRecipesCount = $myRecipes->count();
        $savedRecipesCount = $savedRecipes->count();
        
        // Tổng số lượt thích nhận được trên các công thức đã viết
        $likesReceivedCount = 0;
        if ($myRecipes->isNotEmpty()) {
            $likesReceivedCount = DB::table('recipe_user_likes')
                ->whereIn('recipe_id', $myRecipes->pluck('id'))
                ->count();
        }

        return view('kitchen.index', compact('myRecipes', 'savedRecipes', 'myRecipesCount', 'savedRecipesCount', 'likesReceivedCount'));
    }

    /**
     * Mua công thức nấu ăn trả phí.
     */
    public function purchase(Request $request, Recipe $recipe)
    {
        $user = auth()->user();

        // 1. Kiểm tra xem công thức có bắt buộc trả phí không
        if (!$recipe->is_premium || $recipe->price <= 0) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Công thức này miễn phí, không cần mua!'], 400);
            }
            return back()->with('error', 'Công thức này miễn phí, không cần mua!');
        }

        // 2. Kiểm tra nếu là tác giả, admin, hoặc đã mua rồi
        if ($recipe->user_id === $user->id) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Bạn là tác giả của công thức này!'], 400);
            }
            return back()->with('error', 'Bạn là tác giả của công thức này!');
        }

        if ($user->isAdmin()) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Bạn là Admin, có quyền xem mọi công thức!'], 400);
            }
            return back()->with('error', 'Bạn là Admin, có quyền xem mọi công thức!');
        }

        if ($user->is_premium) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Bạn đã đăng ký gói Premium, có quyền xem mọi công thức!'], 400);
            }
            return back()->with('error', 'Bạn đã đăng ký gói Premium, có quyền xem mọi công thức!');
        }

        $hasPermanentPurchase = $user->purchasedRecipes()
            ->where('recipe_id', $recipe->id)
            ->where('recipe_user_purchases.price', '>', 0)
            ->exists();

        if ($hasPermanentPurchase) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Bạn đã mua vĩnh viễn công thức này trước đó rồi!'], 400);
            }
            return back()->with('error', 'Bạn đã mua vĩnh viễn công thức này trước đó rồi!');
        }

        // 3. Kiểm tra số dư ví
        if ($user->balance < $recipe->price) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Số dư tài khoản không đủ để mua công thức này!'], 400);
            }
            return back()->with('error', 'Số dư tài khoản không đủ! Vui lòng nạp thêm tiền vào ví.');
        }

        try {
            DB::beginTransaction();

            // Trừ tiền người mua
            $user->decrement('balance', $recipe->price);

            // Tính toán thuế (5% bình thường, 1% đối với Premium)
            $author = $recipe->user;
            $taxRate = ($author && $author->is_premium) ? 0.01 : 0.05;
            $taxAmount = $recipe->price * $taxRate;
            $netEarnings = $recipe->price - $taxAmount;

            // Cộng tiền thực nhận cho tác giả
            if ($author) {
                $author->increment('balance', $netEarnings);
            }

            // Chuyển thuế cho tài khoản Admin Kiệt (username: kiet)
            $admin = \App\Models\User::where('username', 'kiet')->first();
            if ($admin) {
                $admin->increment('balance', $taxAmount);
            }

            // Ghi nhận giao dịch mua (nếu đã có bản ghi ad unlock thì update, ngược lại attach)
            $existing = $user->purchasedRecipes()->where('recipe_id', $recipe->id)->first();
            if ($existing) {
                $user->purchasedRecipes()->updateExistingPivot($recipe->id, [
                    'price' => $recipe->price,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $user->purchasedRecipes()->attach($recipe->id, [
                    'price' => $recipe->price,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();

            $message = 'Mua công thức "' . $recipe->title . '" thành công!';
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'balance' => number_format($user->balance, 0, ',', '.') . ' đ',
                ]);
            }

            return redirect()->route('recipe.detail', $recipe->slug)->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Đã có lỗi xảy ra khi thực hiện giao dịch: ' . $e->getMessage()], 500);
            }
            return back()->with('error', 'Giao dịch thất bại: ' . $e->getMessage());
        }
    }

    /**
     * Mở khóa công thức bằng cách xem quảng cáo.
     */
    public function unlockViaAd(Request $request, Recipe $recipe)
    {
        $user = auth()->user();

        // 1. Kiểm tra xem công thức có bắt buộc trả phí không
        if (!$recipe->is_premium || $recipe->price <= 0) {
            return response()->json(['success' => false, 'message' => 'Công thức này miễn phí, không cần mở khóa!'], 400);
        }

        // 2. Chỉ hoạt động với công thức của người dùng bình thường (Master Chef chỉ được trả phí)
        if ($recipe->user && $recipe->user->isMasterChef()) {
            return response()->json(['success' => false, 'message' => 'Công thức của Master Chef chỉ được mua bằng phí, không thể mở khóa bằng quảng cáo!'], 400);
        }

        // 3. Giới hạn xem quảng cáo tối đa 3 lần / 24 giờ
        $adUnlocksCount = DB::table('recipe_user_purchases')
            ->where('user_id', $user->id)
            ->where('price', 0)
            ->where('updated_at', '>=', now()->subHours(24))
            ->count();
        if ($adUnlocksCount >= 3) {
            return response()->json(['success' => false, 'message' => 'Bạn đã sử dụng hết giới hạn xem quảng cáo để mở khóa hôm nay (Tối đa 3 lần/24h).'], 400);
        }

        // 4. Kiểm tra quyền sở hữu hoặc unlock đang hoạt động
        if ($recipe->user_id === $user->id) {
            return response()->json(['success' => false, 'message' => 'Bạn là tác giả của công thức này!'], 400);
        }

        if ($user->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Bạn là Admin, có quyền xem mọi công thức!'], 400);
        }

        if ($user->is_premium) {
            return response()->json(['success' => false, 'message' => 'Bạn đã đăng ký gói Premium, có quyền xem mọi công thức!'], 400);
        }

        $hasActiveUnlock = $user->purchasedRecipes()
            ->where('recipe_id', $recipe->id)
            ->where(function ($q) {
                $q->where('recipe_user_purchases.price', '>', 0)
                  ->orWhere(function ($sub) {
                      $sub->where('recipe_user_purchases.price', 0)
                          ->where('recipe_user_purchases.created_at', '>=', now()->subHours(24));
                  });
            })
            ->exists();

        if ($hasActiveUnlock) {
            return response()->json(['success' => false, 'message' => 'Bạn đã sở hữu hoặc đang trong thời gian mở khóa công thức này!'], 400);
        }

        try {
            DB::beginTransaction();

            $existing = $user->purchasedRecipes()->where('recipe_id', $recipe->id)->first();
            if ($existing) {
                // Cập nhật lại thời gian và đặt giá về 0 (để mở khóa tạm thời qua ad)
                $user->purchasedRecipes()->updateExistingPivot($recipe->id, [
                    'price' => 0.00,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $user->purchasedRecipes()->attach($recipe->id, [
                    'price' => 0.00,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đã mở khóa công thức thành công nhờ xem quảng cáo!',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Mở khóa thất bại: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Hiển thị form chỉnh sửa công thức nấu ăn.
     */
    public function edit(Recipe $recipe)
    {
        // Kiểm tra quyền sở hữu
        if ($recipe->user_id !== auth()->id()) {
            return redirect()->route('home')->with('error', 'Bạn không có quyền chỉnh sửa công thức này!');
        }

        $categories = Category::all();
        
        // Eager load ingredients and steps ordered by order
        $recipe->load(['ingredients', 'steps' => function($q) {
            $q->orderBy('order');
        }]);

        return view('recipe.edit', compact('recipe', 'categories'));
    }

    /**
     * Cập nhật công thức nấu ăn trong cơ sở dữ liệu.
     */
    public function update(Request $request, Recipe $recipe)
    {
        // Kiểm tra quyền sở hữu
        if ($recipe->user_id !== auth()->id()) {
            return redirect()->route('home')->with('error', 'Bạn không có quyền chỉnh sửa công thức này!');
        }

        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'time_to_cook' => ['required', 'integer', 'min:1'],
            'difficulty' => ['required', 'in:dễ,trung bình,khó'],
            'category_id' => ['required', 'exists:categories,id'],
            'image' => ['nullable', 'image', 'max:2048'],
            'tips' => ['nullable', 'string'],
            'ingredients' => ['required', 'array', 'min:1'],
            'ingredients.*.name' => ['required', 'string', 'max:255'],
            'ingredients.*.quantity' => ['nullable', 'string', 'max:255'],
            'ingredients.*.unit' => ['nullable', 'string', 'max:255'],
            'steps' => ['required', 'array', 'min:1'],
            'steps.*.content' => ['required', 'string'],
            'steps.*.image' => ['nullable', 'image', 'max:2048'],
        ], [
            'title.required' => 'Vui lòng nhập tên món ăn.',
            'time_to_cook.required' => 'Vui lòng nhập thời gian nấu.',
            'time_to_cook.integer' => 'Thời gian nấu phải là một số nguyên (phút).',
            'category_id.required' => 'Vui lòng chọn danh mục.',
            'category_id.exists' => 'Danh mục đã chọn không hợp lệ.',
            'image.image' => 'Ảnh đại diện món ăn phải là định dạng hình ảnh.',
            'image.max' => 'Ảnh đại diện món ăn không được vượt quá 2MB.',
            'ingredients.required' => 'Vui lòng nhập ít nhất một nguyên liệu.',
            'ingredients.*.name.required' => 'Vui lòng nhập tên nguyên liệu.',
            'steps.required' => 'Vui lòng nhập ít nhất một bước thực hiện.',
            'steps.*.content.required' => 'Vui lòng nhập nội dung cho bước thực hiện.',
            'steps.*.image.image' => 'Hình ảnh minh họa cho bước nấu phải là định dạng hình ảnh.',
            'steps.*.image.max' => 'Hình ảnh minh họa cho bước nấu không được vượt quá 2MB.',
        ]);

        try {
            DB::beginTransaction();

            // 1. Xử lý ảnh đại diện món ăn
            $imageUrl = $recipe->image;
            if ($request->hasFile('image')) {
                // Xóa ảnh cũ nếu có
                if ($recipe->image) {
                    $oldPath = str_replace('/storage/', '', $recipe->image);
                    Storage::disk('public')->delete($oldPath);
                }
                $path = $request->file('image')->store('recipes', 'public');
                $imageUrl = Storage::url($path);
            }

            // 2. Cập nhật Recipe
            $isPremium = $request->boolean('is_premium');
            $price = $isPremium ? floatval($request->input('price', 0)) : 0.00;

            // Generate slug if title changed
            $slug = $recipe->slug;
            if ($recipe->title !== $request->title) {
                $slug = Str::slug($request->title);
                $originalSlug = $slug;
                $count = 1;
                while (Recipe::where('slug', $slug)->where('id', '!=', $recipe->id)->exists()) {
                    $slug = $originalSlug . '-' . $count;
                    $count++;
                }
            }

            $recipe->update([
                'category_id' => $request->category_id,
                'title' => $request->title,
                'slug' => $slug,
                'description' => $request->description,
                'time_to_cook' => $request->time_to_cook,
                'difficulty' => $request->difficulty,
                'image' => $imageUrl,
                'tips' => $request->tips,
                'is_premium' => $isPremium,
                'price' => $price,
            ]);

            // 3. Cập nhật nguyên liệu (Xóa liên kết cũ, gắn liên kết mới)
            $recipe->ingredients()->detach();
            foreach ($request->ingredients as $ingData) {
                if (empty($ingData['name'])) continue;

                $ingredient = Ingredient::firstOrCreate([
                    'name' => trim($ingData['name'])
                ]);

                $recipe->ingredients()->attach($ingredient->id, [
                    'quantity' => $ingData['quantity'] ?? '',
                    'unit' => $ingData['unit'] ?? ''
                ]);
            }

            // Lấy danh sách các ảnh cũ vẫn được giữ lại (không bị ghi đè bởi ảnh mới)
            $keptOldImages = [];
            foreach ($request->steps as $index => $stepData) {
                if (!empty($stepData['old_image']) && !$request->hasFile("steps.{$index}.image")) {
                    $keptOldImages[] = $stepData['old_image'];
                }
            }

            // 4. Cập nhật các bước nấu ăn (Xóa các bước cũ và lưu các bước mới)
            // Chỉ xóa ảnh vật lý của các bước nếu ảnh đó không nằm trong danh sách được giữ lại
            foreach ($recipe->steps as $oldStep) {
                if ($oldStep->image && !in_array($oldStep->image, $keptOldImages)) {
                    $oldStepPath = str_replace('/storage/', '', $oldStep->image);
                    Storage::disk('public')->delete($oldStepPath);
                }
            }
            $recipe->steps()->delete();

            // Lưu các bước mới
            foreach ($request->steps as $index => $stepData) {
                if (empty($stepData['content'])) continue;

                $stepImageUrl = $stepData['old_image'] ?? null;
                
                // Nếu tải lên ảnh mới cho bước này
                if (isset($stepData['image']) && $request->hasFile("steps.{$index}.image")) {
                    $stepPath = $request->file("steps.{$index}.image")->store('steps', 'public');
                    $stepImageUrl = Storage::url($stepPath);
                }

                Step::create([
                    'recipe_id' => $recipe->id,
                    'order' => $index + 1,
                    'content' => $stepData['content'],
                    'image' => $stepImageUrl,
                ]);
            }

            DB::commit();

            // Tự động kiểm tra nâng cấp cấp bậc
            $oldRole = auth()->user()->role;
            $newRole = auth()->user()->updateCulinaryRole();
            $upgradeMessage = '';
            if ($newRole !== $oldRole) {
                $upgradeMessage = ' Chúc mừng bạn đã thăng cấp thành ' . auth()->user()->role_label . '!';
            }

            return redirect()->route('recipe.detail', $recipe->slug)
                             ->with('success', 'Cập nhật công thức nấu ăn thành công!' . $upgradeMessage);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Đã có lỗi xảy ra khi cập nhật công thức: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Xóa công thức nấu ăn.
     */
    public function destroy(Recipe $recipe)
    {
        // Kiểm tra quyền sở hữu
        if ($recipe->user_id !== auth()->id()) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'Bạn không có quyền xóa công thức này!'], 403);
            }
            return redirect()->route('kitchen.index')->with('error', 'Bạn không có quyền xóa công thức này!');
        }

        try {
            DB::beginTransaction();

            // Xóa ảnh đại diện món ăn
            if ($recipe->image) {
                $oldPath = str_replace('/storage/', '', $recipe->image);
                Storage::disk('public')->delete($oldPath);
            }

            // Xóa ảnh các bước nấu ăn
            foreach ($recipe->steps as $step) {
                if ($step->image) {
                    $oldStepPath = str_replace('/storage/', '', $step->image);
                    Storage::disk('public')->delete($oldStepPath);
                }
            }

            $recipe->delete();

            DB::commit();

            // Tự động cập nhật cấp bậc của người dùng sau khi xóa món
            auth()->user()->updateCulinaryRole();

            $message = 'Đã xóa công thức nấu ăn thành công!';
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                ]);
            }

            return redirect()->route('kitchen.index')->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'Xóa thất bại: ' . $e->getMessage()], 500);
            }
            return redirect()->route('kitchen.index')->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}
