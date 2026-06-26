<?php

namespace App\Observers;

use Illuminate\Database\Eloquent\Model;

abstract class Subject extends Model
{
    /**
     * @var Observer[]
     */
    protected array $observers = [];

    public function attach(Observer $observer): void
    {
        $this->observers[] = $observer;
    }

    public function detach(Observer $observer): void
    {
        foreach ($this->observers as $key => $obs) {
            if ($obs === $observer) {
                unset($this->observers[$key]);
            }
        }
    }

    public function notify(string $message): void
    {
        foreach ($this->observers as $observer) {
            $observer->update($message);
        }
    }
}
