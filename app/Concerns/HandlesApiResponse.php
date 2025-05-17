<?php

namespace App\Concerns;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

/**
 * Trait HandlesApiResponse
 *
 * Provides standardized JSON response methods for API controllers, including
 * success, error, pagination, and internal error handling. Use this trait
 * in controllers to ensure consistent API responses.
 */
trait HandlesApiResponse
{
    /**
     * Determine if verbose errors should be thrown (for local/staging environments).
     *
     * @return bool
     */
    public static function shouldThrowVerboseErrors()
    {
        // if the environment is local or staging, throw verbose errors update in .env
        return app()->environment(['local', 'staging']);
    }

    /**
     * Standard API success response
     *
     * @param  mixed  $data  Response payload
     * @param  string  $message  Descriptive message
     * @param  int  $status  HTTP status code
     */
    public function respondSuccess($data = null, string $message = '', int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'errors' => null,
        ], $status);
    }

    /**
     * Format paginated API responses with meta information.
     *
     * @param  string|null  $resourceClass  API Resource class for transformation
     * @param  string  $message  Optional message
     */
    public function respondWithPagination(AbstractPaginator $paginatedData, ?string $resourceClass = null, string $message = ''): JsonResponse
    {
        $responseData = [
            'items' => $resourceClass
                ? $resourceClass::collection($paginatedData->getCollection())
                : $paginatedData->items(),

            'meta' => [
                'current_page' => $paginatedData->currentPage(),
                'last_page' => $paginatedData->lastPage(),
                'per_page' => $paginatedData->perPage(),
                'total' => $paginatedData->total(),
            ],
        ];

        return $this->respondSuccess($responseData, $message);
    }

    /**
     * Handle internal server errors and return a standardized error response.
     * Throws the exception in local/staging for easier debugging.
     */
    public function respondInternalError(Throwable $exception): JsonResponse
    {

        return self::throwOrReturnInternalError($exception);

    }

    /**
     * Return a standardized JSON error response.
     *
     * @param  string  $message  Error message
     * @param  int  $code  HTTP status code
     * @param  array|null  $errors  Optional error details (hidden in production)
     */
    public function respondError(
        string $message,
        int $code = 422,
        ?array $errors = null
    ): JsonResponse {
        return self::respondFormattedError($message, $code, $errors);

    }

    public static function handleGlobalException(Throwable $exception, Request $request)
    {

        Log::warning('handleGlobalException === class: '.get_class($exception), [
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
        ]);
        // Validation errors (422)

        if ($exception instanceof ValidationException) {
            return self::respondValidationErrors($exception->errors());
        }

           // Authorization errors (403)
           if ($exception instanceof AuthorizationException) {
            return self::throwUnAuthorized('Unauthorized', ['authorization' => $exception->getMessage()]);

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


        return self::throwOrReturnInternalError($exception);

    }

    /**
     * Check if the given response data is a valid Laravel response type.
     *
     * @param  mixed  $responseData
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

    public static function respondValidationErrors($errors)
    {
        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'data' => null,
            'errors' => $errors,
        ], 422);
    }

    /**
     * Throw a formatted unauthenticated response (HTTP 401).
     */
    public static function throwUnAuthenticated(string $message = 'Authentication required', $errors = null): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null,
            'errors' => $errors,
        ], 401);
    }

    public static function throwUnAuthorized(string $message = 'Forbidden', $errors = []): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null,
            'errors' => $errors,
        ], 403);
    }

    /**
     * Handle internal server errors and return a standardized error response.
     * Throws the exception in local/staging for easier debugging.
     */
    public static function throwOrReturnInternalError(Throwable $exception): JsonResponse
    {



        // for testing purposes, throw the exception
        if (self::shouldThrowVerboseErrors()) {
            Log::error($exception->getMessage(), [
                'should throw verbose errors called on throwOrReturnInternalError' => self::shouldThrowVerboseErrors(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
            ]);
            return self::respondFormattedError('Internal server error', 500, [
                'error' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ]);
        }

        $message = 'Something went wrong. Please try again later.';

        if (config('app.env') !== 'production') {
            $message = $exception->getMessage();
        }

        return self::respondFormattedError($message, 500);

    }

    /**
     * Return a standardized JSON error response.
     *
     * @param  string  $message  Error message
     * @param  int  $code  HTTP status code
     * @param  array|null  $errors  Optional error details (hidden in production)
     */
    public static function respondFormattedError(
        string $message,
        int $code = 422,
        ?array $errors = null
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null,
            'errors' => $errors ?? [],
        ], $code);
    }
}
