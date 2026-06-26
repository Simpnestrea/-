<?php

namespace App\Services\RecipeSearch;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class SearchByTime implements SearchStrategy
{
    public function search(Builder $query, ?string $keyword, Request $request): Builder
    {
        $timeRange = $request->input('time'); // quick, medium, long, slow
        
        if ($timeRange) {
            if ($timeRange === 'quick') {
                $query->where('time_to_cook', '<=', 15);
            } elseif ($timeRange === 'medium') {
                $query->whereBetween('time_to_cook', [16, 30]);
            } elseif ($timeRange === 'long') {
                $query->whereBetween('time_to_cook', [31, 60]);
            } elseif ($timeRange === 'slow') {
                $query->where('time_to_cook', '>', 60);
            }
        }
        
        if ($keyword) {
            $query->whereLikeWithoutAccents('title', "%{$keyword}%");
        }
        
        return $query;
    }
}
