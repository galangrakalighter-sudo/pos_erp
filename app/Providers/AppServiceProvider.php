<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Helpers\AdminHelper;

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
        // Share admin identity data to all views
        View::composer('*', function ($view) {
            $view->with('adminIdentity', AdminHelper::getAdminIdentity());
        });
    }
}
