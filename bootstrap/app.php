<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Configuration\Exceptions;
use App\Http\Middleware\CheckRole;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        // api: __DIR__.'/../routes/api.php', // si lo usas
    )
    ->withMiddleware(function (Middleware $middleware) {
        // 👇 ALIAS que usarán tus rutas
        $middleware->alias([
            'role' => CheckRole::class,      // podrás usar 'role:admin' etc.
            'checkrole' => CheckRole::class, // opcional, por compatibilidad
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();