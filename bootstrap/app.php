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
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

return Application::configure(basePath: dirname(__DIR__))
    // Register route files for web, API, console, and health check
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
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
            Log::error('Exception class: ' . get_class($exception));
            // Validation errors (422)
            if ($exception instanceof ValidationException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'data' => null,
                    'errors' => $exception->errors(),
                ], 422);
            }

            // Authorization errors (403)
            if ($exception instanceof AuthorizationException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                    'data' => null,
                    'errors' => ['authorization' => $exception->getMessage()],
                ], 403);
            }

            // Model not found (404)
            if ($exception instanceof ModelNotFoundException) {
                $modelName = strtolower(class_basename($exception->getModel()));
                return response()->json([
                    'success' => false,
                    'message' => "{$modelName} with given id not found",
                    'data' => null,
                    'errors' => ['error' => 'DATA_NOT_FOUND'],
                ], 404);
            }

            // Route not found (404)
            if ($exception instanceof NotFoundHttpException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Route not found',
                    'data' => null,
                    'errors' => ['error' => 'ROUTE_NOT_FOUND'],
                ], 404);
            }

            // Method not allowed (405)
            if ($exception instanceof MethodNotAllowedHttpException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Method not allowed',
                    'data' => null,
                    'errors' => ['method' => 'Invalid HTTP method'],
                ], 405);
            }

            // Too many requests (429)
            if ($exception instanceof ThrottleRequestsException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Too many requests',
                    'data' => null,
                    'errors' => ['throttle' => 'Account locked for some time'],
                ], 429);
            }

            // Generic HTTP exceptions (use status code from exception)
            if ($exception instanceof HttpException) {
                return response()->json([
                    'success' => false,
                    'message' => $exception->getMessage(),
                    'data' => null,
                    'errors' => [],
                ], $exception->getStatusCode());
            }

            // Authentication errors (401)
            if ($exception instanceof Illuminate\Auth\AuthenticationException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated',
                    'data' => null,
                    'errors' => ['authorization' => $exception->getMessage()],
                ], 401);
            }

            // Catch-all for unhandled exceptions (500)
            if ($request->expectsJson()) {
                Log::error($exception); // Log the error for debugging
                return response()->json([
                    'success' => false,
                    'message' => 'Internal server error',
                    'data' => null,
                    'errors' => [],
                ], 500);
            }
        });
    })
    ->create();
