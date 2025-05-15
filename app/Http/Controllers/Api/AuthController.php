<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends BaseApiController
{
    public function register(Request $request)
    {
        return $this->handleRequest(  function() use ($request) {
            $validated = $this->validateRequest([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|unique:users',
                'password' => 'required|string|min:8'
            ]);

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password'])
            ]);

            return [
                'user' => $user,
                'token' => $user->createToken('auth_token')->plainTextToken
            ];
        }, $request, 'User registered successfully');
    }

    public function login(Request $request)
    {
        return $this->handleRequest( function() use ($request) {
            $validated = $this->validateRequest([
                'email' => 'required|email',
                'password' => 'required'
            ]);

            $user = User::where('email', $validated['email'])->first();

            if (! $user || ! Hash::check($validated['password'], $user->password)) {
                $this->errorResponse('Invalid credentials', 401);
            }

            return $user->createToken($request->device_name)->plainTextToken;
        }, $request, 'Login successful');
    }
}
