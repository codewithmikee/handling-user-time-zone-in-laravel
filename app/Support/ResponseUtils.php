<?php
/**
 * name: Mikiyas Birhanu
 * date: 2024-06-09
 * github: https://github.com/codewithmikee
 *
 * This file defines the ResponseUtils class, providing general-purpose helpers for response type checking, safe function execution, error/validation handling, and data validation for use anywhere in the codebase.
 */

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Throwable;

class ResponseUtils
{


    /**
     * Safely run a function and catch any exceptions, logging errors and returning a default response if needed.
     *
     * @param callable $functionToRun
     * @param mixed $defaultResponse
     * @return mixed
     * @throws Throwable If in a verbose environment
     */
    public static function runFunctionSafely($functionToRun, $defaultResponse = null)
    {
        try {
            $response = $functionToRun();
            return $response;
        } catch (Throwable $th) {
            $functionName = debug_backtrace()[1]['function'];
            $message = $th->getMessage();
            Log::error("$functionName : $message");
            if (self::throwError()) {
                throw $th;
            }
            return $defaultResponse;
        }
    }

    /**
     * Determine if errors should be thrown (in local, dev, or staging environments).
     *
     * @return bool
     */
    public static function throwError()
    {
        return app()->environment(['local', 'dev', 'staging']);
    }

    /**
     * Throw a validation exception with a custom message.
     *
     * @param string $message
     * @throws ValidationException
     */
    public static function throwValidationMessage($message)
    {
        throw new ValidationException($message);
    }

    /**
     * Validate the given data against the provided rules and messages.
     *
     * @param array $data Data to validate
     * @param array $rules Validation rules
     * @param array $messages Custom validation messages (optional)
     * @return array Validated data
     * @throws ValidationException If validation fails
     */
    public static function validateData(array $data, array $rules, array $messages = [])
    {
        $validator = Validator::make($data, $rules, $messages);
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
        return $validator->validated();
    }

    /**
     * Get the currently authenticated user from the request using the 'sanctum' guard.
     *
     * @param Request|null $request
     * @return \App\Models\User|null
     */
    public static function getCurrentApiRequestAuthUser(?Request $request)
    {
        if ($request) {
            return $request->user('sanctum');
        }
        // Fallback to global auth if request is not provided
        return auth('sanctum')->user();
    }
}
