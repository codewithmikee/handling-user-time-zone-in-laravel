<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\User;

trait HandlesAuth
{
    use AuthorizesRequests;

    /**
     * Get authenticated user with type hinting
     *
     * @throws HttpResponseException
     */
    protected function currentUser(): User
    {

        // if there is request object, get user from request
        if (isset($this->request)) {
            $user = $this->request->user();
        } else {
            $user = auth('sanctum')->user();
        }

        if (!$user) {
            $this->throwUnAuthenticated();
        }

        return User::find($user->id)->first();
    }

    /**
     * Throw formatted unauthenticated response
     *
     * @param string $message
     * @throws HttpResponseException
     */
    protected function throwUnAuthenticated(string $message = 'Authentication required'): void
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => $message,
            ], 401)
        );
    }

    /**
     * Throw formatted unauthorized response
     *
     * @param string $message
     * @throws AuthorizationException
     */
    protected function throwUnAuthorized(string $message = 'Forbidden'): void
    {
        throw new AuthorizationException($message);
    }
}
