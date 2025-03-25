<?php

namespace App\Providers;
use Illuminate\Support\Facades\Route;
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
      //  $controllerNamespace = 'App\Http\Controllers\Api';

//        Route::prefix('api') // Префикс для всех маршрутов
//        ->middleware('api') // Применяем middleware для API
//        ->namespace($controllerNamespace) // Указываем пространство имен для контроллеров
//        ->group(base_path('routes/api.php')); // Загружаем маршруты из файла api.php

        Route::prefix('api')
        ->middleware('api')
        ->group(base_path('routes/api.php'));
    }
}
