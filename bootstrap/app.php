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

use App\Concerns\HandlesApiResponse;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;

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

        $middleware->use([
            // \App\Http\Middleware\ValidOwnerForPost::class,
        ]);
    })
    // Register global exception handling for API and web
    ->withExceptions(function (Exceptions $exceptions) {
        /**
         * Custom exception rendering for API requests.
         * Returns standardized JSON error responses for common exception types.
         */
        $exceptions->render(function (Throwable $exception, $request) {

                    if ($exception instanceof ValidationException) {

                        return HandlesApiResponse::respondValidationErrors($exception->errors());

                    }

                // Authorization errors (403)
                if ($exception instanceof AuthorizationException) {
                    return HandlesApiResponse::throwUnAuthorized('Unauthorized', ['authorization' => $exception->getMessage()]);

                }

                if ($exception instanceof ModelNotFoundException) {
                    $modelName = strtolower(class_basename($exception->getModel()));
                    $message = "{$modelName} with given id not found";

                    return response()->json(['error' => 'DATA_NOT_FOUND', 'message' => $message], 404);
                }

                if ($exception instanceof MethodNotAllowedException) {
                    return response()->json(['error' => 'Method Not Allowed'], 405);
                }




                if ($exception instanceof ThrottleRequestsException) {

                    return response()->json(['error' => 'Account locked for sometime'], 429);
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

                if ($exception instanceof NotFoundHttpException) {

                    return response()->json([
                        'success' => false,
                        'message' => "Resource not found",
                        'data' => null,
                        'errors' => ['error' => 'DATA_NOT_FOUND'],
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
                if ($exception instanceof AuthenticationException) {
                    return self::throwUnAuthenticated('Unauthenticated', ['authorization' => $exception->getMessage()]);
                }
            if ($request->expectsJson()) {

                return HandlesApiResponse::handleGlobalException($exception, $request);
            }
            return parent::render($request, $exception);
        });
    })
    ->create();
