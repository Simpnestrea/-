<?php

namespace App\Services\RecipeSearch;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

interface SearchStrategy
{
    /**
     * Apply search constraints to the query builder.
     *
     * @param Builder $query
     * @param string|null $keyword
     * @param Request $request
     * @return Builder
     */
    public function search(Builder $query, ?string $keyword, Request $request): Builder;
}
