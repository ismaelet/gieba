<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Sentry\Laravel\Integration;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->validateCsrfTokens(except: [
            'legacy-admin/login',
            'legacy-admin/logout',
            'legacy-admin/gallery/list',
            'legacy-admin/orders/add',
            'legacy-admin/orders/list',
            'legacy-admin/orders/merge',
            'legacy-admin/products/add',
            'legacy-admin/products/list',
            'legacy-admin/products/merge',
            'legacy-admin/settings/sections/add',
            'legacy-admin/settings/sections/list',
            'legacy-admin/users/add',
            'legacy-admin/users/list',
            'legacy-admin/users/merge',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        Integration::handles($exceptions);
    })->create();
