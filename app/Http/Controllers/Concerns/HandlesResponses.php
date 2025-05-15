<?php
/**
 * name: Mikiyas Birhanu
 * date: 2024-06-09
 * github: https://github.com/codewithmikee
 *
 * This file defines the HandlesResponses trait, providing standardized success and error response helpers for API controllers, as well as authorization validation.
 */

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;

trait HandlesResponses
{
    /**
     * Return a standardized JSON success response.
     *
     * @param mixed $data The data to return
     * @param string $message Optional message
     * @param int $code HTTP status code (default 200)
     * @return JsonResponse
     */
    protected function successResponse(
        mixed $data,
        string $message = '',
        int $code = 200
    ): JsonResponse {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data,
            'errors' => null,
        ], $code);
    }

    /**
     * Return a standardized JSON error response.
     *
     * @param string $message Error message
     * @param int $code HTTP status code
     * @param array|null $errors Optional error details (hidden in production)
     * @return JsonResponse
     */
    protected function errorResponse(
        string $message,
        int $code,
        ?array $errors = null
    ): JsonResponse {
        return response()->json([
            'status' => false,
            'message' => $message,
            'data' => null,
            // Only show error details if not in production
            'errors' => App::isProduction() ? [] : $errors,
        ], $code);
    }

    /**
     * Throws an AuthorizationException if the current user is not authenticated via Sanctum.
     *
     * @throws AuthorizationException
     */
    protected function validateAuthorization(): void
    {
        if (!auth('sanctum')->check()) {
            throw new AuthorizationException('Unauthenticated');
        }
    }

     /**
     * Check if the given response data is a valid Laravel response type.
     *
     * @param mixed $responseData
     * @return bool
     */
    public function isResponseType($responseData)
    {
        $responseClasses = [
            'Illuminate\\Http\\Response',
            'Illuminate\\Http\\JsonResponse',
            'Illuminate\\Http\\ResponseFactory',
            'Illuminate\\Http\\JsonResponseFactory',
        ];
        foreach ($responseClasses as $class) {
            if (is_a($responseData, $class)) {
                return true;
            }
        }
        return false;
    }
}
