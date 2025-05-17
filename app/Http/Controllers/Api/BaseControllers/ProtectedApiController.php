<?php

/**
 * name: Mikiyas Birhanu
 * date: 2024-06-09
 * github: https://github.com/codewithmikee
 *
 * This file defines the ProtectedApiController which extends BaseApiController and applies authentication middleware for protected API endpoints.
 */

namespace App\Http\Controllers\Api\BaseControllers;

use App\Concerns\HandlesAuth;
use Illuminate\Http\Request;

/**
 * Class ProtectedApiController
 *
 * Extends BaseApiController and applies Sanctum authentication middleware.
 * Use this as a base for all controllers that require authenticated access.
 */
class ProtectedApiController extends BaseApiController
{
    use HandlesAuth;

    /**
     * Constructor injects the current HTTP request and ensures parent initialization.
     */
    public function __construct(Request $request)
    {
        parent::__construct($request); // Proper parent constructor call
    }

    /**
     * Returns the middleware to be applied to this controller.
     *
     * @return array
     *
     * This method statically declares the 'auth:sanctum' middleware for all routes in this controller.
     */
    public static function middleware(): array
    {
        return [
            'auth:sanctum', // Sanctum authentication middleware
        ];
    }
}
