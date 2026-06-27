<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;

// 1. Trang chủ
Route::get('/', function () {
    $featuredRecipes = \App\Models\Recipe::with('category')->approved()->inRandomOrder()->take(3)->get();
    $categories = \App\Models\Category::inRandomOrder()->take(4)->get();
    $famousRecipes = \App\Models\Recipe::with('category')->approved()->orderBy('id', 'desc')->take(12)->get();
    
    $easyRecipes = \App\Models\Recipe::with('category')->approved()->where('difficulty', 'dễ')->inRandomOrder()->take(12)->get();
    
    $quickRecipes = \App\Models\Recipe::with('category')->approved()->where('time_to_cook', '<=', 30)->inRandomOrder()->take(12)->get();

    $premiumRecipes = \App\Models\Recipe::with('category')->approved()
        ->where('is_premium', true)
        ->where('price', '>', 0)
        ->withCount(['likedByUsers', 'savedByUsers', 'comments'])
        ->orderByRaw('(liked_by_users_count + saved_by_users_count + comments_count) DESC')
        ->take(10)
        ->get();

    return view('welcome', compact('featuredRecipes', 'categories', 'famousRecipes', 'easyRecipes', 'quickRecipes', 'premiumRecipes'));
})->name('home');

// Route gợi ý tìm kiếm (Autocomplete AJAX)
Route::get('/api/search-suggestions', function (Illuminate\Http\Request $request) {
    $query = $request->query('query', '');
    if (!$query) {
        return response()->json([]);
    }
    $recipes = \App\Models\Recipe::select('id', 'title', 'slug', 'image')
        ->approved()
        ->whereLikeWithoutAccents('title', "%{$query}%")
        ->take(8)
        ->get();
    return response()->json($recipes);
});

// 2. Trang kết quả tìm kiếm
Route::get('/search', [\App\Http\Controllers\SearchController::class, 'index'])->name('search');

// 3. Các trang chức năng (Yêu cầu đăng nhập)
Route::middleware('auth')->group(function () {
    Route::get('/premium', function () { return view('premium'); })->name('premium');
    Route::get('/stats', function (Illuminate\Http\Request $request) {
        $userId = auth()->id();
        $period = $request->input('period', '30'); // '30', '7', 'all'

        $userRecipes = \App\Models\Recipe::where('user_id', $userId);
        $recipeIds = $userRecipes->pluck('id');

        // 1. Tổng số lượt xem của tất cả công thức của người dùng
        $totalViews = $userRecipes->sum('views_count');

        // 2. Số lượt thích trong khoảng thời gian được chọn
        $likesQuery = \Illuminate\Support\Facades\DB::table('recipe_user_likes')
            ->whereIn('recipe_id', $recipeIds);
        if ($period === '7') {
            $likesQuery->where('created_at', '>=', now()->subDays(7));
        } elseif ($period === '30') {
            $likesQuery->where('created_at', '>=', now()->subDays(30));
        }
        $likesCount = $likesQuery->count();

        // 3. Số lượt lưu trong khoảng thời gian được chọn
        $savesQuery = \Illuminate\Support\Facades\DB::table('recipe_user_saves')
            ->whereIn('recipe_id', $recipeIds);
        if ($period === '7') {
            $savesQuery->where('created_at', '>=', now()->subDays(7));
        } elseif ($period === '30') {
            $savesQuery->where('created_at', '>=', now()->subDays(30));
        }
        $savesCount = $savesQuery->count();

        // 4. Món ăn nổi bật: 5 món có lượt xem nhiều nhất của người dùng
        $topRecipes = \App\Models\Recipe::where('user_id', $userId)
            ->orderBy('views_count', 'desc')
            ->take(5)
            ->get()
            ->map(function ($recipe) use ($period) {
                // Đếm lượt thích trong chu kỳ lọc
                $likesInPeriodQuery = \Illuminate\Support\Facades\DB::table('recipe_user_likes')
                    ->where('recipe_id', $recipe->id);
                if ($period === '7') {
                    $likesInPeriodQuery->where('created_at', '>=', now()->subDays(7));
                } elseif ($period === '30') {
                    $likesInPeriodQuery->where('created_at', '>=', now()->subDays(30));
                }
                $recipe->likes_in_period = $likesInPeriodQuery->count();

                // Đếm lượt lưu trong chu kỳ lọc
                $savesInPeriodQuery = \Illuminate\Support\Facades\DB::table('recipe_user_saves')
                    ->where('recipe_id', $recipe->id);
                if ($period === '7') {
                    $savesInPeriodQuery->where('created_at', '>=', now()->subDays(7));
                } elseif ($period === '30') {
                    $savesInPeriodQuery->where('created_at', '>=', now()->subDays(30));
                }
                $recipe->saves_in_period = $savesInPeriodQuery->count();

                return $recipe;
            });

        return view('stats', compact('totalViews', 'likesCount', 'savesCount', 'topRecipes', 'period'));
    })->name('stats');
    Route::get('/interactions', function () {
        $userId = auth()->id();

        // 1. Bình luận trên công thức của tôi (loại trừ bình luận của tôi)
        $comments = \App\Models\Comment::whereHas('recipe', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->where('user_id', '!=', $userId)
            ->with(['user', 'recipe'])
            ->get()
            ->map(function ($comment) {
                return [
                    'type' => 'comment',
                    'user' => $comment->user,
                    'recipe' => $comment->recipe,
                    'content' => $comment->content,
                    'created_at' => $comment->created_at,
                ];
            });

        // 2. Lượt thích trên công thức của tôi (loại trừ lượt thích của tôi)
        $likes = \Illuminate\Support\Facades\DB::table('recipe_user_likes')
            ->join('recipes', 'recipe_user_likes.recipe_id', '=', 'recipes.id')
            ->join('users', 'recipe_user_likes.user_id', '=', 'users.id')
            ->where('recipes.user_id', $userId)
            ->where('recipe_user_likes.user_id', '!=', $userId)
            ->select('recipe_user_likes.created_at', 'users.id as user_id', 'users.name as user_name', 'users.avatar as user_avatar', 'recipes.title as recipe_title', 'recipes.slug as recipe_slug', 'recipes.id as recipe_id')
            ->get()
            ->map(function ($like) {
                return [
                    'type' => 'like',
                    'user' => (object)[
                        'id' => $like->user_id,
                        'name' => $like->user_name,
                        'avatar' => $like->user_avatar,
                    ],
                    'recipe' => (object)[
                        'id' => $like->recipe_id,
                        'title' => $like->recipe_title,
                        'slug' => $like->recipe_slug,
                    ],
                    'content' => null,
                    'created_at' => \Carbon\Carbon::parse($like->created_at),
                ];
            });

        // 3. Lượt lưu trên công thức của tôi (loại trừ lượt lưu của tôi)
        $saves = \Illuminate\Support\Facades\DB::table('recipe_user_saves')
            ->join('recipes', 'recipe_user_saves.recipe_id', '=', 'recipes.id')
            ->join('users', 'recipe_user_saves.user_id', '=', 'users.id')
            ->where('recipes.user_id', $userId)
            ->where('recipe_user_saves.user_id', '!=', $userId)
            ->select('recipe_user_saves.created_at', 'users.id as user_id', 'users.name as user_name', 'users.avatar as user_avatar', 'recipes.title as recipe_title', 'recipes.slug as recipe_slug', 'recipes.id as recipe_id')
            ->get()
            ->map(function ($save) {
                return [
                    'type' => 'save',
                    'user' => (object)[
                        'id' => $save->user_id,
                        'name' => $save->user_name,
                        'avatar' => $save->user_avatar,
                    ],
                    'recipe' => (object)[
                        'id' => $save->recipe_id,
                        'title' => $save->recipe_title,
                        'slug' => $save->recipe_slug,
                    ],
                    'content' => null,
                    'created_at' => \Carbon\Carbon::parse($save->created_at),
                ];
            });

        // Gộp và sắp xếp
        $interactions = $comments->concat($likes)->concat($saves)->sortByDesc('created_at');

        return view('interactions', compact('interactions'));
    })->name('interactions');
    Route::get('/my-kitchen', [\App\Http\Controllers\RecipeController::class, 'myKitchen'])->name('kitchen.index');
    
    Route::get('/recipe/create', [\App\Http\Controllers\RecipeController::class, 'create'])->name('recipe.create');
    Route::post('/recipe/create', [\App\Http\Controllers\RecipeController::class, 'store'])->name('recipe.store');
    Route::get('/recipe/{recipe}/edit', [\App\Http\Controllers\RecipeController::class, 'edit'])->name('recipe.edit');
    Route::put('/recipe/{recipe}/update', [\App\Http\Controllers\RecipeController::class, 'update'])->name('recipe.update');
    Route::delete('/recipe/{recipe}/destroy', [\App\Http\Controllers\RecipeController::class, 'destroy'])->name('recipe.destroy');
    Route::post('/recipe/{recipe}/comment', [\App\Http\Controllers\CommentController::class, 'store'])->name('comment.store');
    Route::post('/recipe/{recipe}/like', [\App\Http\Controllers\RecipeController::class, 'toggleLike'])->name('recipe.like');
    Route::post('/recipe/{recipe}/save', [\App\Http\Controllers\RecipeController::class, 'toggleSave'])->name('recipe.save');
    Route::post('/comment/{comment}/react', [\App\Http\Controllers\CommentController::class, 'toggleReaction'])->name('comment.react');
    Route::post('/comment/{comment}/report', [\App\Http\Controllers\CommentController::class, 'report'])->name('comment.report');
    Route::put('/comment/{comment}', [\App\Http\Controllers\CommentController::class, 'update'])->name('comment.update');
    Route::delete('/comment/{comment}', [\App\Http\Controllers\CommentController::class, 'destroy'])->name('comment.destroy');
    Route::post('/recipe/{recipe}/purchase', [\App\Http\Controllers\RecipeController::class, 'purchase'])->name('recipe.purchase');
    Route::post('/recipe/{recipe}/unlock-via-ad', [\App\Http\Controllers\RecipeController::class, 'unlockViaAd'])->name('recipe.unlock-via-ad');
    Route::get('/wallet', [\App\Http\Controllers\WalletController::class, 'index'])->name('wallet.index');
    Route::post('/wallet/deposit', [\App\Http\Controllers\WalletController::class, 'deposit'])->name('wallet.deposit');
    Route::post('/premium/buy', [\App\Http\Controllers\WalletController::class, 'buyPremium'])->name('premium.buy');
});

// 4. Các trang công khai khác
Route::get('/recipe/{slug}', function ($slug) {  
    $recipe = \App\Models\Recipe::with([
        'user', 
        'category', 
        'ingredients', 
        'steps' => function($q) { $q->orderBy('order'); },
        'comments' => function($q) { 
            $q->latest()
              ->withCount(['reactions as likes_count' => function($query) {
                  $query->where('type', 'like');
              }])
              ->withCount(['reactions as dislikes_count' => function($query) {
                  $query->where('type', 'dislike');
              }]);
        },
        'comments.user',
        'comments.reactions' => function($q) {
            if (auth()->check()) {
                $q->where('user_id', auth()->id());
            }
        }
    ])->where('slug', $slug)->firstOrFail();
    return view('recipe.detail', compact('recipe'));
})->name('recipe.detail');

// 5. Auth & Đăng bài
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate'])->name('login.post');

    Route::get('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/register', [AuthController::class, 'store'])->name('register.post');

    // Đăng nhập/Đăng ký qua mạng xã hội
    Route::get('/auth/{provider}/redirect', [\App\Http\Controllers\SocialAuthController::class, 'redirectToProvider'])->name('social.redirect');
    Route::get('/auth/{provider}/callback', [\App\Http\Controllers\SocialAuthController::class, 'handleProviderCallback'])->name('social.callback');
    Route::get('/auth/social/complete', [\App\Http\Controllers\SocialAuthController::class, 'showCompleteForm'])->name('social.complete');
    Route::post('/auth/social/complete', [\App\Http\Controllers\SocialAuthController::class, 'completeRegistration'])->name('social.complete.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// 6. Quản trị viên (Admin Dashboard)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [\App\Http\Controllers\AdminController::class, 'index'])->name('dashboard');
    
    // Quản lý thành viên (Users)
    Route::get('/users', [\App\Http\Controllers\AdminController::class, 'users'])->name('users');
    Route::get('/users/{user}/detail', [\App\Http\Controllers\AdminController::class, 'userDetail'])->name('users.detail');
    Route::post('/users/{user}/toggle-admin', [\App\Http\Controllers\AdminController::class, 'toggleAdmin'])->name('users.toggle-admin');
    Route::post('/users/{user}/toggle-premium', [\App\Http\Controllers\AdminController::class, 'togglePremium'])->name('users.toggle-premium');
    Route::post('/users/{user}/change-role', [\App\Http\Controllers\AdminController::class, 'changeRole'])->name('users.change-role');
    Route::delete('/users/{user}', [\App\Http\Controllers\AdminController::class, 'deleteUser'])->name('users.delete');
    
    // Quản lý công thức nấu ăn (Recipes)
    Route::get('/recipes', [\App\Http\Controllers\AdminController::class, 'recipes'])->name('recipes');
    Route::get('/recipes/{recipe}/detail', [\App\Http\Controllers\AdminController::class, 'recipeDetail'])->name('recipes.detail');
    Route::post('/recipes/{recipe}/publish', [\App\Http\Controllers\AdminController::class, 'publishRecipe'])->name('recipes.publish');
    Route::post('/recipes/{recipe}/reject', [\App\Http\Controllers\AdminController::class, 'rejectRecipe'])->name('recipes.reject');
    Route::delete('/recipes/{recipe}', [\App\Http\Controllers\AdminController::class, 'deleteRecipe'])->name('recipes.delete');
    
    // Quản lý danh mục (Categories)
    Route::get('/categories', [\App\Http\Controllers\AdminController::class, 'categories'])->name('categories');
    Route::post('/categories', [\App\Http\Controllers\AdminController::class, 'storeCategory'])->name('categories.store');
    Route::post('/categories/{category}', [\App\Http\Controllers\AdminController::class, 'updateCategory'])->name('categories.update');
    Route::delete('/categories/{category}', [\App\Http\Controllers\AdminController::class, 'deleteCategory'])->name('categories.delete');
    
    // Quản lý tố cáo (Comment Reports)
    Route::get('/reports', [\App\Http\Controllers\AdminController::class, 'reports'])->name('reports');
    Route::post('/reports/{report}/dismiss', [\App\Http\Controllers\AdminController::class, 'dismissReport'])->name('reports.dismiss');
    Route::delete('/reports/{report}/comment', [\App\Http\Controllers\AdminController::class, 'deleteReportedComment'])->name('reports.delete-comment');
});