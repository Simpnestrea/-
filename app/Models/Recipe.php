<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Observers\Subject;
use App\Models\User;
use App\Models\Comment;

class Recipe extends Subject
{
    /** @use HasFactory<\Database\Factories\RecipeFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id', 'category_id', 'title', 'slug', 'description', 
        'time_to_cook', 'difficulty', 'image', 'views_count', 'tips', 'is_premium', 'price', 'status'
    ];

    /**
     * Scope a query to only include approved recipes.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'Approved');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function steps()
    {
        return $this->hasMany(Step::class);
    }

    public function ingredients()
    {
        return $this->belongsToMany(Ingredient::class, 'recipe_ingredient')
                    ->withPivot('quantity', 'unit')
                    ->withTimestamps();
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function likedByUsers()
    {
        return $this->belongsToMany(User::class, 'recipe_user_likes')->withTimestamps();
    }

    public function savedByUsers()
    {
        return $this->belongsToMany(User::class, 'recipe_user_saves')->withTimestamps();
    }

    public function buyers()
    {
        return $this->belongsToMany(User::class, 'recipe_user_purchases')->withPivot('price')->withTimestamps();
    }

    /**
     * Handle like interaction and notify observers.
     */
    public function like(User $user)
    {
        $message = "Người dùng {$user->name} đã thích công thức '{$this->title}' của bạn.";
        // [BẢN PHAO BẢO VỆ ĐỒ ÁN - OBSERVER PATTERN]
        // -> ĐÂY LÀ HÀM "notify()" CỦA SUBJECT: Nó sẽ lặp qua tất cả Observer đang theo dõi và gọi hàm update() để bắn thông báo.
        $this->notify($message);
    }

    /**
     * Handle comment interaction and notify observers.
     */
    public function comment(Comment $comment)
    {
        $message = "Người dùng {$comment->user->name} đã bình luận về công thức '{$this->title}': {$comment->content}";
        $this->notify($message);
    }
}
