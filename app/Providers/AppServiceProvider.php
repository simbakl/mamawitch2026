<?php

namespace App\Providers;

use App\View\Composers\LayoutComposer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        // Share nav/footer/social data with all layout views (single batch load)
        View::composer('layouts.app', LayoutComposer::class);

        // Log lazy loading in development to catch N+1 (non-blocking)
        Model::handleLazyLoadingViolationUsing(function ($model, $relation) {
            logger()->warning("Lazy loading [{$relation}] on [" . get_class($model) . ']');
        });
    }
}
