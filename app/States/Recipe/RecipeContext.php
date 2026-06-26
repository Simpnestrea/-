<?php

namespace App\States\Recipe;

use App\Models\Recipe;

class RecipeContext
{
    private RecipeState $state;
    private Recipe $recipe;

    public function __construct(Recipe $recipe)
    {
        $this->recipe = $recipe;
        
        $this->state = match($recipe->status) {
            'Approved' => new ApprovedState(),
            'Rejected' => new RejectedState(),
            default => new PendingState(),
        };
    }

    public function setState(RecipeState $state): void
    {
        $this->state = $state;
        
        // Cập nhật Database
        $this->recipe->status = $state->getStatusName();
        $this->recipe->save();
    }

    public function getRecipe(): Recipe
    {
        return $this->recipe;
    }

    public function publish(): void
    {
        $this->state->publish($this);
    }

    public function reject(): void
    {
        $this->state->reject($this);
    }

    public function getStatusName(): string
    {
        return $this->state->getStatusName();
    }
}
