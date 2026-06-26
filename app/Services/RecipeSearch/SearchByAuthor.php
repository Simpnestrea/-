<?php

namespace App\Services\RecipeSearch;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class SearchByAuthor implements SearchStrategy
{
    public function search(Builder $query, ?string $keyword, Request $request): Builder
    {
        if ($keyword) {
            $query->whereHas('user', function ($q) use ($keyword) {
                $q->whereLikeWithoutAccents('name', "%{$keyword}%")
                  ->orWhereLikeWithoutAccents('username', "%{$keyword}%");
            });
        }
        
        return $query;
    }
}
