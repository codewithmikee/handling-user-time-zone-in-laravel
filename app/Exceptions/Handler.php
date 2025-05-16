<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Auth\Access\AuthorizationException;

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
     * @param \Illuminate\Http\Request $request
     * @param \Throwable $e
     * @return Response
     */
    public function render($request, Throwable $e): Response
    {
        // Handle validation errors (422)
        if ($e instanceof ValidationException) {
            return response()->json([
                'success' => false,
                'errors'  => $e->errors(),
            ], 422);
        }

        // Handle model not found (404)
        if ($e instanceof ModelNotFoundException) {
            return response()->json([
                'success' => false,
                'message' => 'Resource not found',
            ], 404);
        }

        // Handle unauthorized (403)
        if ($e instanceof AuthorizationException) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 403);
        }

        // Generic error (only show details in non-production)
        $response = ['success' => false];

        if (config('app.env') !== 'production') {
            $response['message'] = $e->getMessage();
            $response['trace'] = $e->getTrace();
        } else {
            $response['message'] = 'Server Error';
        }

        return response()->json($response, 500);
    }
}
