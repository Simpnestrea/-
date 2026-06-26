<?php

namespace App\States\Recipe;

class ApprovedState implements RecipeState
{
    public function publish(RecipeContext $context): void
    {
        // Already approved
        throw new \Exception("Công thức này đã được duyệt rồi.");
    }

    public function reject(RecipeContext $context): void
    {
        $context->setState(new RejectedState());
    }

    public function getStatusName(): string
    {
        return 'Approved';
    }
}
