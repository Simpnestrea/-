<?php

namespace App\Services\RecipeSearch;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class SearchByCategory implements SearchStrategy
{
    public function search(Builder $query, ?string $keyword, Request $request): Builder
    {
        $categorySlug = $request->input('category');
        
        if ($categorySlug) {
            $query->whereHas('category', function ($q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            });
        }
        
        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->whereLikeWithoutAccents('title', "%{$keyword}%")
                  ->orWhereHas('category', function ($q2) use ($keyword) {
                      $q2->whereLikeWithoutAccents('name', "%{$keyword}%");
                  });
            });
        }
        
        return $query;
    }
}
