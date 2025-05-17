<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseControllers\BaseApiController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * Controller for authentication endpoints (register, login).
 *
 * Uses BaseApiController for standardized validation and responses.
 */
class AuthController extends BaseApiController
{
    /**
     * Register a new user and return user data with token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        return $this->handleRequest(function () use ($request) {
            $validated = $this->validateRequest($request, [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|unique:users',
                'password' => 'required|string|min:8',
            ]);

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            return [
                'user' => $user,
                'token' => $user->createToken('auth_token')->plainTextToken,
            ];
        }, $request, 'User registered successfully');
    }

    /**
     * Login a user and return an access token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        return $this->handleRequest(function () use ($request) {
            $validated = $this->validateRequest($request, [
                'email' => 'required|email',
                'password' => 'required',
            ]);

            $user = User::where('email', $validated['email'])->first();

            if (! $user || ! Hash::check($validated['password'], $user->password)) {
                return $this->respondError('Invalid credentials', 401);
            }

            return $user->createToken($request->device_name)->plainTextToken;
        }, $request, 'Login successful');
    }

    private function generateToken(Request $request)
    {
        $token = $request->user()->createToken($request->token_name);

        return ['token' => $token->plainTextToken];
    }
}
