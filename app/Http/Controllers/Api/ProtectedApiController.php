<?php
/**
 * name: Mikiyas Birhanu
 * date: 2024-06-09
 * github: https://github.com/codewithmikee
 *
 * This file defines the ProtectedApiController which extends BaseApiController and applies authentication middleware for protected API endpoints.
 */

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Concerns\{HandlesAuth};
class ProtectedApiController extends BaseApiController
{
    use  HandlesAuth;
    /**
     * Constructor injects the current HTTP request and ensures parent initialization.
     *
     * @param Request $request
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
            'auth:sanctum' // Sanctum authentication middleware
        ];
    }
}
