<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\SetLocaleMiddleware::class,
            \App\Http\Middleware\AuthenticatedNoCacheMiddleware::class,
        ]);
        $middleware->validateCsrfTokens(except: [
            'webhooks/xendit/invoices',
        ]);

        $middleware->redirectGuestsTo(fn (Request $request) => route('landing'));

        $middleware->alias([
            'action.auth' => \App\Http\Middleware\ActionAuthMiddleware::class,
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
