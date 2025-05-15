<?php
/**
 * name: Mikiyas Birhanu
 * date: 2024-06-09
 * github: https://github.com/codewithmikee
 *
 * This file defines the BaseApiController which provides common API controller utilities such as request validation, user retrieval, and standardized response/error handling for API endpoints.
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Concerns\HandlesResponses;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Support\ResponseUtils;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class BaseApiController extends Controller
{
    use HandlesResponses;
    /** @var Request The request instance available to all methods */
    protected $request;

    /**
     * Indicates if verbose errors should be thrown (for local/staging environments).
     * @var bool
     */
    protected $shouldThrowVerboseErrors;

    /**
     * Constructor injects the current HTTP request and sets error verbosity based on environment.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
        // Enable verbose errors in local or staging environments
        $this->shouldThrowVerboseErrors = app()->environment(['local', 'staging']);
    }

    /**
     * Validate the request data against given rules.
     *
     * @param array $rules Validation rules
     * @param array $messages Custom validation messages (optional)
     * @param array|null $data Data to validate (defaults to request data if null)
     * @return array Validated data
     * @throws ValidationException If validation fails
     *
     * This method uses Laravel's Validator to check the request data. Throws a ValidationException if validation fails.
     */
    public function validateRequest(array $rules, array $messages = [], $data = null)
    {
        $data = $data ?? $this->request?->all();
        $validator = Validator::make($data, $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }

    /**
     * Retrieve the currently authenticated user for the API request.
     *
     * @return \App\Models\User|null
     *
     * Uses ResponseUtils to get the user from the request (Sanctum guard).
     */
    public function getCurrentUser()
    {
        return ResponseUtils::getCurrentApiRequestAuthUser($this->request);
    }

    /**
     * Handles execution of a function with standardized success/error response.
     *
     * @param callable $functionToRun The function to execute
     * @param string $message Success message to return
     * @return \Illuminate\Http\JsonResponse|mixed
     *
     * This method wraps the function execution in a try/catch, returning a success response or handling exceptions.
     */
    public function handleRequest( $functionToRun, $request = null, string $message = 'Operation successful')
    {
        try {
            $request = $request ?? $this->request;
            $response = $functionToRun() ?? [];
            // If the response is already a valid response type, return it directly
            if (isset($response) || !empty($response)) {
                if ($this->isResponseType($response)) {
                    return $response;
                }
            }
            // Otherwise, wrap in a standardized success response
            return $this->successResponse($response, $message);
        } catch (\Throwable $e) {
            // Handle and log exceptions
            return $this->handleException($e);
        }
    }

    /**
     * Handles exceptions and returns a standardized error response.
     *
     * @param \Throwable $th
     * @return \Illuminate\Http\JsonResponse
     *
     * Logs the error and, if in a verbose environment, rethrows. Otherwise, returns a generic error response.
     */
    public function handleException(\Throwable $th)
    {
        Log::error($th);
        if ($this->shouldThrowVerboseErrors) {
            throw $th;
        }
        return response()->json([
            'status' => false,
            'message' => 'Unknown error occurred',
            'error' => 'Something went wrong please try again',
        ], 500);
    }
}
