<?php

namespace App\Services\RecipeSearch;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class SearchByTitle implements SearchStrategy
{
    public function search(Builder $query, ?string $keyword, Request $request): Builder
    {
        if ($keyword) {
            $query->whereLikeWithoutAccents('title', "%{$keyword}%");
        }
        
        return $query;
    }
}
