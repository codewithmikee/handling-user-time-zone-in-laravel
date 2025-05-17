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
    use HandlesApiResponse;
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

            return $validator->validated();

    }
}
