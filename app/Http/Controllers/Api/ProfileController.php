<?php

/**
 * name: Mikiyas Birhanu
 * date: 2024-06-09
 * github: https://github.com/codewithmikee
 *
 * This file defines the ProfileController which extends ProtectedApiController and provides a method to fetch the user's profile.
 */

namespace App\Http\Controllers\Api;

use App\Http\Requests\StorePostRequest;
use Illuminate\Http\Request;

/**
 * Controller for fetching the authenticated user's profile.
 *
 * Uses handleRequest and currentUser helpers for standardized logic.
 */
class ProfileController extends ProtectedApiController
{
    /**
     * Get the authenticated user's profile (name, email).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        return $this->handleRequest(function () {
            return $this->currentUser()->only('name', 'email');
        }, $request, 'Profile fetched successfully');
    }

    public function posts(StorePostRequest $request)
    {
        return $this->handleRequest(function () {
            return $this->currentUser()->posts;
        }, $request, 'Posts fetched successfully');
    }
}
