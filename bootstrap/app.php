<?php

use App\Http\Middleware\AdminMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    // You don't need the then() callback for middleware in Laravel 11
    // Middleware is now configured in withMiddleware()
    )
    ->withMiddleware(function (Middleware $middleware) {

        $middleware->alias([
            'admin' => AdminMiddleware::class,
        ]);

        $middleware->statefulApi(); // Активирует поддержку cookies


//        $middleware->appendToGroup('api', [
//            \App\Http\Middleware\SanctumUnauthorizedRedirect::class,
//            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
//        ]);

        $middleware->api(prepend: [
            \App\Http\Middleware\SanctumUnauthorizedRedirect::class,
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            \Illuminate\Http\Middleware\HandleCors::class,
        ]);
        $middleware->web(append: [
            \Illuminate\Http\Middleware\HandleCors::class,
        ]);



        $middleware->trustProxies(at: [
            '127.0.0.1',
            'localhost',
        ]);
    })
    ->withProviders([
        Laravel\Sanctum\SanctumServiceProvider::class,
    ])
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
