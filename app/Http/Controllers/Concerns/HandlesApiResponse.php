<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\AbstractPaginator;
use Throwable;

trait HandlesApiResponse
{
    public function shouldThrowVerboseErrors()
    {
        // if the environment is local or staging, throw verbose errors update in .env
        return app()->environment(['local', 'staging']);
    }

    /**
     * Standard API success response
     *
     * @param mixed $data    Response payload
     * @param string $message Descriptive message
     * @param int $status   HTTP status code
     * @return JsonResponse
     */
    protected function respondSuccess($data = null, string $message = '', int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => $data,
            'message' => $message,
        ], $status);
    }

    /**
     * Format paginated API responses
     *
     * @param AbstractPaginator $paginatedData
     * @param string|null $resourceClass  API Resource class for transformation
     * @return JsonResponse
     */
    protected function respondWithPagination(AbstractPaginator $paginatedData, ?string $resourceClass = null, string $message = ''): JsonResponse
    {
        $responseData = [
            'items' => $resourceClass
                ? new $resourceClass($paginatedData->getCollection())
                : $paginatedData->items(),

            'meta' => [
                'current_page' => $paginatedData->currentPage(),
                'last_page'    => $paginatedData->lastPage(),
                'per_page'     => $paginatedData->perPage(),
                'total'        => $paginatedData->total(),
            ]
        ];

        return $this->respondSuccess($responseData, $message);
    }

    /**
     * Handle internal server errors
     *
     * @param Throwable $exception
     * @return JsonResponse
     */
    protected function respondInternalError(Throwable $exception): JsonResponse
    {
        // for testing purposes, throw the exception
        if($this->shouldThrowVerboseErrors()) {
            throw $exception;
        } else {
            $message = 'Something went wrong. Please try again later.';
        }

        return $this->respond(null, $message, 500);
    }

    /**
     * Return a standardized JSON error response.
     *
     * @param string $message Error message
     * @param int $code HTTP status code
     * @param array|null $errors Optional error details (hidden in production)
     * @return JsonResponse
     */
    protected function respondError(
        string $message,
        int $code = 422,
        ?array $errors = null
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null,
            // Only show error details if not in production
            'errors' =>  $errors ?? [],
        ], $code);
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
