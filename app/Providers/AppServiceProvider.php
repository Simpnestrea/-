<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Builder;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Database\Eloquent\Builder::macro('whereLikeWithoutAccents', function ($attribute, $searchTerm) {
            return $this->where($attribute, 'LIKE', $searchTerm);
        });

        \Illuminate\Database\Eloquent\Builder::macro('orWhereLikeWithoutAccents', function ($attribute, $searchTerm) {
            return $this->orWhere($attribute, 'LIKE', $searchTerm);
        });

        \Illuminate\Database\Query\Builder::macro('whereLikeWithoutAccents', function ($attribute, $searchTerm) {
            return $this->where($attribute, 'LIKE', $searchTerm);
        });

        \Illuminate\Database\Query\Builder::macro('orWhereLikeWithoutAccents', function ($attribute, $searchTerm) {
            return $this->orWhere($attribute, 'LIKE', $searchTerm);
        });
    }
}
