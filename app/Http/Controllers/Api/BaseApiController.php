<?php
/**
 * name: Mikiyas Birhanu
 * date: 2024-06-09
 * github: https://github.com/codewithmikee
 *
 * This file defines the BaseApiController which provides common API controller utilities such as request validation, user retrieval, and standardized response/error handling for API endpoints.
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Concerns\{HandlesApiResponse, HandlesValidation};
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * Class BaseApiController
 *
 * Provides reusable API controller logic, including request validation and
 * standardized response/error handling. Extend this class for all API controllers.
 */
class BaseApiController extends Controller
{
    use HandlesApiResponse, HandlesValidation;

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
    }

    /**
     * Validate the request data against given rules.
     *
     * @param Request $request The HTTP request
     * @param array $rules Validation rules
     * @param array $messages Custom validation messages (optional)
     * @return array Validated data
     * @throws ValidationException If validation fails
     *
     * This method uses Laravel's Validator to check the request data. Throws a ValidationException if validation fails.
     */
    public function validateRequest(Request $request, array $rules, array $messages = [])
    {
        return $this->validate($request->all(), $rules, $messages);
    }

    /**
     * Handles execution of a function with standardized success/error response.
     *
     * @param callable $functionToRun The function to execute
     * @param Request|null $request The HTTP request (optional)
     * @param string $message Success message to return
     * @return \Illuminate\Http\JsonResponse|mixed
     *
     * This method wraps the function execution in a try/catch, returning a success response or handling exceptions.
     */
    public function handleRequest($functionToRun, $request = null, string $message = 'Operation successful')
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
            return $this->respondSuccess($response, $message);
        } catch (\Throwable $e) {
            // Handle and log exceptions
            return $this->respondInternalError($e);
        }
    }
}
