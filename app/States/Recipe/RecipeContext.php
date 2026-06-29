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
        // [BẢN PHAO BẢO VỆ ĐỒ ÁN - STATE PATTERN]
        // -> Đây là hàm chuyển đổi trạng thái (Transition). 
        // Khi State gọi hàm này, Context sẽ cập nhật trạng thái mới.
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
        // [BẢN PHAO BẢO VỆ ĐỒ ÁN - STATE PATTERN]
        // -> Context không tự xử lý mà "Ủy quyền" (Delegate) cho State hiện tại xử lý hành động publish.
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
