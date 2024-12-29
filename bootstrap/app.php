<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'languages' => App\Http\Middleware\Languages::class,
        ]);
    })->withRouting(
        commands: __DIR__ . '/../routes/console.php',
        channels: __DIR__ . '/../routes/channels.php',
        using: function () {
            Route::middleware(['web', 'languages'])
                ->group(base_path('routes/web.php'));
        },
    )
    // ->withRouting(
    //     web: __DIR__.'/../routes/web.php',
    //     commands: __DIR__.'/../routes/console.php',
    // channels: __DIR__.'/../routes/channels.php',
    //     health: '/up',
    // )
    ->withExceptions(function (Exceptions $exceptions) {})->create();
