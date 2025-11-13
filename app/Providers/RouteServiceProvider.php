<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
class RouteServiceProvider extends ServiceProvider
{
    /**
     * Register services.
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
        // Las rutas API se cargan directamente desde bootstrap/app.php
        // Este método ya no es necesario, pero se mantiene por compatibilidad
        // Si necesitas el prefijo 'v1', descomenta las siguientes líneas:
        // Route::prefix('v1')
        //     ->middleware('api')
        //     ->group(base_path('routes/api.php'));
    }


}
