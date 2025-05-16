<?php

/**
 * name: Mikiyas Birhanu
 * date: 2024-06-09
 * github: https://github.com/codewithmikee
 *
 * This file defines the ProfileController which extends BaseApiController and provides a method to fetch the user's profile.
 */


namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

class ProfileController extends ProtectedApiController
{
    public function index(Request $request)
    {
        return $this->handleRequest( function() use ($request) {
            return $this->currentUser()->only('name', 'email');
        }, $request, 'Profile fetched successfully');
    }
}
