<?php

namespace App\Observers;

interface Observer
{
    public function update(string $message): void;
}
