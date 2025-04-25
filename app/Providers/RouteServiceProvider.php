<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->routes(function () {
            // Charge les routes web
            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            // Charge les routes API
            Route::prefix('api') // <-- très important !
                ->middleware('api')
                ->group(base_path('routes/api.php'));
        });
    }
}
