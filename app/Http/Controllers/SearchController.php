<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Recipe;
use App\Models\Category;
use App\Models\Ingredient;
use App\Services\RecipeSearch\RecipeService;
use App\Services\RecipeSearch\SearchByTitle;
use App\Services\RecipeSearch\SearchByIngredient;
use App\Services\RecipeSearch\SearchByCategory;
use App\Services\RecipeSearch\SearchByAuthor;
use App\Services\RecipeSearch\SearchByTime;

class SearchController extends Controller
{
    protected RecipeService $recipeService;

    public function __construct(RecipeService $recipeService)
    {
        $this->recipeService = $recipeService;
    }

    public function index(Request $request)
    {
        $type = $request->input('type');
        $query = $request->input('query', '');

        // Map old sidebar queries if necessary
        if ($query === 'Danh mục') { $type = 'category'; $query = ''; }
        elseif ($query === 'Nguyên liệu') { $type = 'ingredient'; $query = ''; }
        elseif ($query === 'Món ăn') { $type = 'dish'; $query = ''; }
        elseif ($query === 'Thời gian nấu') { $type = 'time'; $query = ''; }

        if (!$type) {
            $type = $query ? 'dish' : 'category';
        }

        // [BẢN PHAO BẢO VỆ ĐỒ ÁN - STRATEGY PATTERN]
        // -> ĐÂY LÀ "CONTEXT": Lớp RecipeService đóng vai trò là Context chứa chiến lược.
        // Dựa vào input của User, hệ thống quyết định chọn thuật toán tìm kiếm nào (ByTitle, ByCategory...).
        $strategy = match ($type) {
            'category' => new SearchByCategory(),
            'ingredient' => new SearchByIngredient(),
            'author' => new SearchByAuthor(),
            'time' => new SearchByTime(),
            default => new SearchByTitle(), // 'dish' or anything else
        };

        // -> GỌI "setStrategy": Nhúng (Inject) thuật toán vừa chọn vào Context
        $this->recipeService->setStrategy($strategy);

        $recipesQuery = Recipe::with(['user', 'category', 'ingredients'])->approved();
        
        // -> "EXECUTE (Thực thi)": Context chạy thuật toán tìm kiếm mà không cần biết logic bên trong từng file Strategy viết gì.
        $recipesQuery = $this->recipeService->searchRecipe($recipesQuery, $query, $request);

        $recipes = $recipesQuery->orderBy('id', 'desc')->get();

        // Load helper data for selectors
        $categories = Category::all();
        
        // Get popular ingredients by counting their recipes in the DB (Cached for 1 hour)
        $popularIngredients = \Illuminate\Support\Facades\Cache::remember('popular_ingredients', 3600, function () {
            return Ingredient::withCount('recipes')
                ->orderBy('recipes_count', 'desc')
                ->take(12)
                ->get();
        });

        return view('search', compact('query', 'type', 'recipes', 'categories', 'popularIngredients'));
    }
}
