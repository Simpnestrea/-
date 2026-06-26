<?php

namespace App\Services\RecipeSearch;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class SearchByIngredient implements SearchStrategy
{
    public function search(Builder $query, ?string $keyword, Request $request): Builder
    {
        $ingredientName = $request->input('ingredient');
        
        if ($ingredientName) {
            $query->whereHas('ingredients', function ($q) use ($ingredientName) {
                $q->where('name', $ingredientName);
            });
        }
        
        if ($keyword) {
            $query->whereHas('ingredients', function ($q) use ($keyword) {
                $q->whereLikeWithoutAccents('name', "%{$keyword}%");
            });
        }
        
        return $query;
    }
}
