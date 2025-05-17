<?php

namespace App\Concerns;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * Trait HandlesAuth
 *
 * Provides helper methods for retrieving the authenticated user and throwing
 * standardized authentication/authorization error responses. Use in protected controllers.
 */
trait HandlesAuth
{
    use AuthorizesRequests, HandlesApiResponse;

    /**
     * Get the currently authenticated user.
     */
    private function currentAuthenticatedUser(): ?Authenticatable
    {
        // If there is a request object, get user from request; otherwise use auth('sanctum')
        $user = auth('sanctum')->user();

        return $user;
    }

    /**
     * Get the currently authenticated user's ID.
     */
    public function getCurrentUserId(): int
    {
        $user = $this->currentAuthenticatedUser();
        if (! $user) {
            return \App\Concerns\HandlesApiResponse::throwUnAuthenticated();
        }

        return $user->id;
    }

    /**
     * Get the currently authenticated user (type-hinted).
     *
     * @throws HttpResponseException If not authenticated
     */
public function currentUser(): Authenticatable
{
    $user = $this->currentAuthenticatedUser();
    if (!$user) {
        $this->throwUnAuthenticated();
    }
    return $user; // Directly return the authenticated user
}
}
