<?php

namespace App\Builders;

use App\Models\Recipe;

interface RecipeBuilderInterface
{
    /**
     * Thêm thông tin cơ bản cho công thức.
     */
    public function addBasicInfo(array $data, ?string $imageUrl = null): self;

    /**
     * Thêm nhiều nguyên liệu vào công thức.
     */
    public function addIngredients(array $ingredientsData): self;

    /**
     * Thêm các bước thực hiện vào công thức.
     */
    public function addSteps(array $stepsData, ?array $stepImages = null): self;

    /**
     * Xây dựng và trả về đối tượng Recipe hoàn chỉnh.
     */
    public function build(): Recipe;
}
