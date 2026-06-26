<?php

namespace App\Observers;

use App\Models\User;
use App\Models\ActivityLog;

class UserAuthor implements Observer
{
    protected User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function update(string $message): void
    {
        ActivityLog::create([
            'user_id' => $this->user->id,
            'message' => $message,
        ]);
    }
}
