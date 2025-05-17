<?php

/**
 * Laravel Application Bootstrapper
 *
 * name: Mikiyas Birhanu
 * date: 2024-06-09
 * github: https://github.com/codewithmikee
 *
 * This file configures the Laravel application, including routing, middleware, and exception handling.
 * It ensures all API responses are JSON and provides detailed, standardized error responses for API consumers.
 */

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Exception classes for custom error handling

return Application::configure(basePath: dirname(__DIR__))
    // Register route files for web, API, console, and health check
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    // Register global middleware
    ->withMiddleware(function (Middleware $middleware) {
        // Enable stateful API for Sanctum
        $middleware->statefulApi();
        // Ensure all API responses are JSON
        $middleware->api(prepend: [
            \App\Http\Middleware\EnsureJsonResponse::class,
        ]);
    })
    // Register global exception handling for API and web
    ->withExceptions(function (Exceptions $exceptions) {
        /**
         * Custom exception rendering for API requests.
         * Returns standardized JSON error responses for common exception types.
         */
        $exceptions->render(function (Throwable $exception, $request) {
            return \App\Concerns\HandlesApiResponse::handleGlobalException($exception, $request);
        });
    })
    ->create();
