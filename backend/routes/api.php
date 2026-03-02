<?php

use App\Http\Controllers\Auth\TokenAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/auth/register', [TokenAuthController::class, 'register']);
Route::post('/auth/login', [TokenAuthController::class, 'login']);
Route::post('/auth/forgot-password/request', [TokenAuthController::class, 'requestPasswordResetCode']);
Route::post('/auth/forgot-password/verify', [TokenAuthController::class, 'verifyPasswordResetCode']);
Route::post('/auth/forgot-password/reset', [TokenAuthController::class, 'resetPasswordWithCode']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/me', [TokenAuthController::class, 'me']);
    Route::post('/auth/logout', [TokenAuthController::class, 'logout']);

    // Compatibility for existing Vue store calls.
    Route::get('/all.user', [TokenAuthController::class, 'allUsers']);
    Route::get('/reffer.user', [TokenAuthController::class, 'referredUsers']);
});
