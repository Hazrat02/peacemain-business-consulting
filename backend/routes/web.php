<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Auth\TokenAuthController;
use App\Http\Controllers\User\UserDashboardController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Public/Home');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [TokenAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [TokenAuthController::class, 'webLogin'])->name('login.submit');
    Route::get('/register', [TokenAuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [TokenAuthController::class, 'webRegister'])->name('register.submit');

    // Backward compatibility for old frontend/cached assets.
    Route::get('/admin/login', [TokenAuthController::class, 'showLogin']);
    Route::post('/admin/login', [TokenAuthController::class, 'webLogin']);
});

Route::middleware('auth')->post('/logout', [TokenAuthController::class, 'webLogout'])->name('logout');
Route::middleware('auth')->post('/admin/logout', [TokenAuthController::class, 'webLogout']);
Route::middleware('auth')->get('/dashboard', UserDashboardController::class)->name('user.dashboard');

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', AdminDashboardController::class)->name('admin.dashboard');
});
