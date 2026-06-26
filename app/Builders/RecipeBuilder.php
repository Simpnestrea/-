<?php

namespace App\Builders;

use App\Models\Recipe;
use App\Models\Ingredient;
use App\Models\Step;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class RecipeBuilder implements RecipeBuilderInterface
{
    protected Recipe $recipe;

    public function __construct()
    {
        $this->recipe = new Recipe();
    }

    public function addBasicInfo(array $data, ?string $imageUrl = null): self
    {
        $slug = Str::slug($data['title']);
        $originalSlug = $slug;
        $count = 1;
        while (Recipe::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        $this->recipe->user_id = auth()->id();
        $this->recipe->category_id = $data['category_id'];
        $this->recipe->title = $data['title'];
        $this->recipe->slug = $slug;
        $this->recipe->description = $data['description'] ?? null;
        $this->recipe->time_to_cook = $data['time_to_cook'];
        $this->recipe->difficulty = $data['difficulty'];
        $this->recipe->tips = $data['tips'] ?? null;
        $this->recipe->views_count = 0;
        
        $isPremium = isset($data['is_premium']) ? (bool) $data['is_premium'] : false;
        $this->recipe->is_premium = $isPremium;
        $this->recipe->price = $isPremium ? floatval($data['price'] ?? 0) : 0.00;
        
        if ($imageUrl) {
            $this->recipe->image = $imageUrl;
        }

        // Lưu thông tin cơ bản trước để có ID gán cho các mối quan hệ (relationships)
        $this->recipe->save();

        return $this;
    }

    public function addIngredients(array $ingredientsData): self
    {
        foreach ($ingredientsData as $ingData) {
            if (empty($ingData['name'])) continue;

            $ingredient = Ingredient::firstOrCreate([
                'name' => trim($ingData['name'])
            ]);

            $this->recipe->ingredients()->attach($ingredient->id, [
                'quantity' => $ingData['quantity'] ?? '',
                'unit' => $ingData['unit'] ?? ''
            ]);
        }

        return $this;
    }

    public function addSteps(array $stepsData, ?array $stepImages = null): self
    {
        foreach ($stepsData as $index => $stepData) {
            if (empty($stepData['content'])) continue;

            $stepImageUrl = null;
            if ($stepImages && isset($stepImages[$index]['image'])) {
                $stepPath = $stepImages[$index]['image']->store('steps', 'public');
                $stepImageUrl = Storage::url($stepPath);
            }

            Step::create([
                'recipe_id' => $this->recipe->id,
                'order' => $index + 1,
                'content' => $stepData['content'],
                'image' => $stepImageUrl,
            ]);
        }

        return $this;
    }

    public function build(): Recipe
    {
        return $this->recipe;
    }
}
