<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Providers\AppServiceProvider;
use App\Http\Middleware\TrustProxies;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    // 1) Register your AppServiceProvider
    ->withProviders([
        AppServiceProvider::class,
    ])

    // 2) Set up your routes as before
    ->withRouting(
        web:      __DIR__.'/../routes/web.php',
        api:      __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health:   '/up',
    )

    // 3) Register global middleware: 
    //    a) TrustProxies first, then your existing aliases
    ->withMiddleware(function (Middleware $middleware) {
        // Trust Render’s load‑balancer headers, including X-Forwarded-Proto
        $middleware->append(TrustProxies::class);

        // Your existing route‑middleware aliases
        $middleware->alias([
            'role'              => RoleMiddleware::class,
            'permission'        => PermissionMiddleware::class,
            'role_or_permission'=> RoleOrPermissionMiddleware::class,
        ]);
    })

    // 4) Exception handling (unchanged)
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })

    ->create();
