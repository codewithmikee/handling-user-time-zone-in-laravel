<?php

use App\Http\Controllers\Api\ApiControllerTest;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

Route::get('/', [ApiControllerTest::class, 'index']);

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/not-found', function (Request $request) {
    return response()->json(['message' => 'Not found'], 404);
});
