<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Auth\Access\AuthorizationException;

class Handler extends ExceptionHandler
{
    public function render($request, Throwable $e): Response
    {
        // Handle validation errors
        if ($e instanceof ValidationException) {
            return response()->json([
                'success' => false,
                'errors'  => $e->errors(),
            ], 422);
        }

        // Handle model not found
        if ($e instanceof ModelNotFoundException) {
            return response()->json([
                'success' => false,
                'message' => 'Resource not found',
            ], 404);
        }

        // Handle unauthorized
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
