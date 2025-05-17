<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Custom exception handler for API responses.
 *
 * Converts common exceptions to standardized JSON responses for API consumers.
 */
class Handler extends ExceptionHandler
{
    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function render($request, Throwable $e): Response
    {
        // Handle validation errors (422)
        if ($e instanceof \Illuminate\Validation\ValidationException) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'data' => null,
                'errors' => $e->errors(),
            ], 422);
        }

        // Handle model not found (404)
        if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
            return response()->json([
                'success' => false,
                'message' => 'Resource not found',
                'data' => null,
                'errors' => ['error' => 'DATA_NOT_FOUND'],
            ], 404);
        }

        // Handle unauthorized (403)
        if ($e instanceof \Illuminate\Auth\Access\AuthorizationException) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null,
                'errors' => ['error' => 'FORBIDDEN'],
            ], 403);
        }

        // Generic error (only show details in non-production)
        $response = [
            'success' => false,
            'message' => config('app.env') !== 'production' ? $e->getMessage() : 'Server Error',
            'data' => null,
            'errors' => config('app.env') !== 'production' ? [$e->getMessage()] : [],
        ];

        if (config('app.env') !== 'production') {
            $response['trace'] = $e->getTrace();
        }

        return response()->json($response, 500);
    }
}
