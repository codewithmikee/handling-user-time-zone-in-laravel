<?php

namespace App\Concerns;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Trait HandlesValidation
 *
 * Provides reusable validation logic for API controllers. Use this trait to
 * validate request data and return standardized validation error responses.
 */
trait HandlesValidation
{
    /**
     * Validate request data and return validated data array.
     *
     * @param  array  $dataToValidate  The data to validate (usually $request->all())
     * @param  array  $rules  Validation rules
     * @param  array  $messages  Custom error messages (optional)
     * @return array Validated data
     *
     * @throws HttpResponseException If validation fails
     */
    protected function validate($dataToValidate, array $rules, array $messages = []): array
    {
        $validator = Validator::make($dataToValidate, $rules, $messages);

        if ($validator->fails()) {
            $this->respondCustomValidation($validator->errors()->toArray());
        }

        return $validator->validated();
    }

    /**
     * Throw a custom validation error response (HTTP 422 Unprocessable Entity).
     *
     * @param  array  $errors  Key-value array of validation errors
     * @param  int  $status  HTTP status code (default: 422)
     *
     * @throws HttpResponseException
     */
    protected function respondCustomValidation(array $errors, int $status = 422): void
    {
        throw new \Illuminate\Http\Exceptions\HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'data' => null,
                'errors' => $errors,
            ], $status)
        );
    }
}
