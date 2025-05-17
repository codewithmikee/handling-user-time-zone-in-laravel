<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TimeZoneTestController;

Route::get('/', function () {
    return view('welcome');
});

// routes/web.php
Route::get('/timezone-converter', [TimeZoneTestController::class, 'show']);
Route::post('/timezone-converter', [TimeZoneTestController::class, 'convert']);
