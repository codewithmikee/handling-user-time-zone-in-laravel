<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

trait HandlesValidation
{
    /**
     * Validate request and return validated data
     *
     * @param array $dataToValidate
     * @param array $rules
     * @param array $messages Custom error messages
     * @return array
     *
     * @throws HttpResponseException
     */
    protected function validate( $dataToValidate, array $rules, array $messages = []): array
    {
        $validator = Validator::make($dataToValidate, $rules, $messages);

        if ($validator->fails()) {
            $this->respondCustomValidation($validator->errors()->toArray());
        }

        return $validator->validated();
    }

    /**
     * Throw custom validation error response
     *
     * @param array $errors  Key-value array of errors
     * @param int $status    HTTP status code
     * @throws HttpResponseException
     */
    protected function respondCustomValidation(array $errors, int $status = 422): void
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'errors'  => $errors,
            ], $status)
        );
    }
}
