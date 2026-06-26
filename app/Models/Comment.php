<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    /** @use HasFactory<\Database\Factories\CommentFactory> */
    use HasFactory;

    protected $fillable = ['user_id', 'recipe_id', 'content'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }

    /**
     * Lấy các lượt thích/không thích của bình luận này.
     */
    public function reactions()
    {
        return $this->hasMany(CommentReaction::class);
    }

    /**
     * Lấy các lượt tố cáo của bình luận này.
     */
    public function reports()
    {
        return $this->hasMany(CommentReport::class);
    }
}
