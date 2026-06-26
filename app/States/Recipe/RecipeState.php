<?php

namespace App\States\Recipe;

interface RecipeState
{
    public function publish(RecipeContext $context): void;
    public function reject(RecipeContext $context): void;
    public function getStatusName(): string;
}
