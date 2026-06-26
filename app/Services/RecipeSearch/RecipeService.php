<?php

namespace App\Services\RecipeSearch;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class RecipeService
{
    protected SearchStrategy $strategy;

    public function __construct(SearchStrategy $strategy = null)
    {
        if ($strategy) {
            $this->strategy = $strategy;
        }
    }

    public function setStrategy(SearchStrategy $strategy): void
    {
        $this->strategy = $strategy;
    }

    /**
     * @param Builder $query
     * @param string|null $keyword
     * @param Request $request
     * @return Builder
     */
    public function searchRecipe(Builder $query, ?string $keyword, Request $request): Builder
    {
        if (!isset($this->strategy)) {
            throw new \RuntimeException('SearchStrategy is not set.');
        }

        return $this->strategy->search($query, $keyword, $request);
    }
}
