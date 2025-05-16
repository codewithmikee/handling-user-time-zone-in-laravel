<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\User;

/**
 * Trait HandlesAuth
 *
 * Provides helper methods for retrieving the authenticated user and throwing
 * standardized authentication/authorization error responses. Use in protected controllers.
 */
trait HandlesAuth
{
    use AuthorizesRequests;

    /**
     * Get the currently authenticated user (type-hinted).
     *
     * @return User
     * @throws HttpResponseException If not authenticated
     */
    protected function currentUser(): User
    {
        // If there is a request object, get user from request; otherwise use auth('sanctum')
        if (isset($this->request)) {
            $user = $this->request->user();
        } else {
            $user = auth('sanctum')->user();
        }

        if (!$user) {
            $this->throwUnAuthenticated();
        }

        // Always return a fresh User instance
        return User::find($user->id)->first();
    }

    /**
     * Throw a formatted unauthenticated response (HTTP 401).
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
     * Throw a formatted unauthorized response (HTTP 403).
     *
     * @param string $message
     * @throws AuthorizationException
     */
    protected function throwUnAuthorized(string $message = 'Forbidden'): void
    {
        throw new AuthorizationException($message);
    }
}
