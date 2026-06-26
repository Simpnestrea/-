<?php

namespace App\States\Recipe;

class RejectedState implements RecipeState
{
    public function publish(RecipeContext $context): void
    {
        $context->setState(new ApprovedState());
    }

    public function reject(RecipeContext $context): void
    {
        // Already rejected
        throw new \Exception("Công thức này đã bị từ chối rồi.");
    }

    public function getStatusName(): string
    {
        return 'Rejected';
    }
}
