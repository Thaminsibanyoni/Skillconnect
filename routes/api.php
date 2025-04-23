<?php

use App\Http\Controllers\Api\ProviderLocationController; // Import controller
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Route for providers to update their location
// Requires Sanctum authentication and the custom 'auth.provider' middleware
Route::middleware(['auth:sanctum', 'auth.provider'])->post('/provider/location', [ProviderLocationController::class, 'update']);
