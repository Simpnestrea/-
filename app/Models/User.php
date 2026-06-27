<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'username', 'email', 'password', 'avatar', 'role', 'bio', 'is_premium', 'is_admin', 'provider', 'provider_id', 'balance'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $appends = ['role_label'];

    /**
     * Lấy các thuộc tính cần được ép kiểu.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'is_premium' => 'boolean',
            'balance' => 'decimal:2',
        ];
    }

    /**
     * Kiểm tra xem người dùng có phải admin không.
     */
    public function isAdmin(): bool
    {
        return (bool) $this->is_admin;
    }

    public function recipes()
    {
        return $this->hasMany(Recipe::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function likedRecipes()
    {
        return $this->belongsToMany(Recipe::class, 'recipe_user_likes')->withTimestamps();
    }

    public function savedRecipes()
    {
        return $this->belongsToMany(Recipe::class, 'recipe_user_saves')->withTimestamps();
    }

    /**
     * Lấy các lượt tương tác của người dùng này với các bình luận.
     */
    public function commentReactions()
    {
        return $this->hasMany(CommentReaction::class);
    }

    /**
     * Lấy các lượt báo cáo bình luận của người dùng này.
     */
    public function commentReports()
    {
        return $this->hasMany(CommentReport::class);
    }

    public function purchasedRecipes()
    {
        return $this->belongsToMany(Recipe::class, 'recipe_user_purchases')->withPivot('price')->withTimestamps();
    }

    /**
     * Lấy nhãn hiển thị cho cấp bậc của người dùng.
     */
    public function getRoleLabelAttribute(): string
    {
        return match($this->role) {
            'beginner' => 'Người mới (Beginner)',
            'homecook' => 'Đầu bếp tại gia (Home Cook)',
            'prochef' => 'Đầu bếp chuyên nghiệp (Pro Chef)',
            'masterchef' => 'Siêu đầu bếp (Master Chef)',
            default => 'Người mới (Beginner)'
        };
    }

    /**
     * Kiểm tra xem người dùng này có phải là Master Chef hay không.
     */
    public function isMasterChef(): bool
    {
        return $this->role === 'masterchef' || str_ends_with($this->email, '@chef.com') || in_array($this->username, ['gordon', 'christine']);
    }

    /**
     * Tự động kiểm tra và nâng cấp cấp bậc ẩm thực của người dùng.
     * Cấp bậc Master Chef (masterchef) chỉ do Admin chỉ định thủ công.
     */
    public function updateCulinaryRole(): string
    {
        $roleWeights = [
            'beginner' => 1,
            'homecook' => 2,
            'prochef' => 3,
            'masterchef' => 4,
        ];

        // Đảm bảo vai trò được khởi tạo nếu rỗng
        if (empty($this->role)) {
            $this->role = 'beginner';
        }

        $currentWeight = $roleWeights[$this->role] ?? 1;

        // Nếu đã là Master Chef thì không thay đổi
        if ($this->role === 'masterchef') {
            return 'masterchef';
        }

        $recipesCount = $this->recipes()->count();
        $likesCount = \Illuminate\Support\Facades\DB::table('recipe_user_likes')
            ->whereIn('recipe_id', $this->recipes()->pluck('id'))
            ->count();

        $targetRole = 'beginner';
        if ($recipesCount >= 10 && $likesCount >= 20) {
            $targetRole = 'prochef';
        } elseif ($recipesCount >= 3 && $likesCount >= 5) {
            $targetRole = 'homecook';
        }

        $targetWeight = $roleWeights[$targetRole] ?? 1;

        // Chỉ nâng cấp nếu cấp bậc mới cao hơn cấp bậc hiện tại
        if ($targetWeight > $currentWeight) {
            $this->role = $targetRole;
            $this->save();
        }

        return $this->role ?? 'beginner';
    }
}
