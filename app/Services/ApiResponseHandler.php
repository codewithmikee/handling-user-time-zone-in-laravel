<?php
/**
 * name: Mikiyas Birhanu
 * date: 2024-06-09
 * github: https://github.com/codewithmikee
 *
 * This file is deprecated. All general-purpose response and error utilities have been moved to App\Support\ResponseUtils.
 * Only controller-specific helpers (like throwNotFoundError) should remain here if needed.
 */

namespace App\Services;

// DEPRECATED: Use App\Support\ResponseUtils instead for general-purpose helpers.

use Illuminate\Http\JsonResponse;

class ApiResponseHandler
{
    /**
     * Return a standardized 404 not found error response (controller-specific).
     *
     * @param string $message
     * @return JsonResponse
     */
    public static function throwNotFoundError($message)
    {
        return response()->json([
            'status' => true,
            'error' => 'DATA_NOT_FOUND',
            'message' => $message,
        ], 404);
    }
}
