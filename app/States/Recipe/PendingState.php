<?php

namespace App\States\Recipe;

class PendingState implements RecipeState
{
    public function publish(RecipeContext $context): void
    {
        $context->setState(new ApprovedState());
    }

    public function reject(RecipeContext $context): void
    {
        $context->setState(new RejectedState());
    }

    public function getStatusName(): string
    {
        return 'Pending';
    }
}
