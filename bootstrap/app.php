<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->api([
            \Illuminate\Http\Middleware\HandleCors::class,
        ]);
        
        // Middleware de desarrollo para autenticación automática
        $env = $_ENV['APP_ENV'] ?? 'production';
        if (in_array($env, ['local', 'development'])) {
            $middleware->alias([
                'dev.auth' => \App\Http\Middleware\DevAuthMiddleware::class,
            ]);
        }
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
