<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

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
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->mapApiRoutes();
    }
    protected function mapApiRoutes(): void
    {
        Route::prefix('v1')
            ->middleware('api')
            ->group(base_path('routes/api.php'));
    }
}
