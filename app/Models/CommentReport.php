<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommentReport extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'comment_id', 'reason'];

    /**
     * Mối quan hệ với người dùng.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mối quan hệ với bình luận.
     */
    public function comment()
    {
        return $this->belongsTo(Comment::class);
    }
}
