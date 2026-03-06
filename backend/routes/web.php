<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\ProfileController;
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
    Route::get('/forgot-password', [TokenAuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [TokenAuthController::class, 'webForgotPassword'])->name('password.email');

    // Backward compatibility for old frontend/cached assets.
    Route::get('/admin/login', [TokenAuthController::class, 'showLogin']);
    Route::post('/admin/login', [TokenAuthController::class, 'webLogin']);
});

Route::middleware('auth')->post('/logout', [TokenAuthController::class, 'webLogout'])->name('logout');
Route::middleware('auth')->post('/admin/logout', [TokenAuthController::class, 'webLogout']);
Route::middleware('auth')->get('/dashboard', UserDashboardController::class)->name('user.dashboard');
Route::middleware('auth')->get('/dashboard/documents', [UserDashboardController::class, 'documents'])->name('user.documents');
Route::middleware('auth')->get('/dashboard/overseas', [UserDashboardController::class, 'overseas'])->name('user.overseas');
Route::middleware('auth')->get('/profile', [ProfileController::class, 'userEdit'])->name('user.profile.edit');
Route::middleware('auth')->patch('/profile', [ProfileController::class, 'userUpdate'])->name('user.profile.update');
Route::middleware('auth')->patch('/profile/password', [ProfileController::class, 'userUpdatePassword'])->name('user.profile.password.update');

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', AdminDashboardController::class)->name('admin.dashboard');
    Route::get('/profile', [ProfileController::class, 'adminEdit'])->name('admin.profile.edit');
    Route::patch('/profile', [ProfileController::class, 'adminUpdate'])->name('admin.profile.update');
    Route::patch('/profile/password', [ProfileController::class, 'adminUpdatePassword'])->name('admin.profile.password.update');
    Route::get('/users', [AdminDashboardController::class, 'users'])->name('admin.users');
    Route::get('/contact-us', [AdminDashboardController::class, 'contactUs'])->name('admin.contact-us');
    Route::get('/roles', [AdminDashboardController::class, 'roles'])->name('admin.roles');
    Route::get('/content/banner', [AdminDashboardController::class, 'contentBanner'])->name('admin.content.banner');
    Route::get('/content/sidebar', [AdminDashboardController::class, 'contentSidebar'])->name('admin.content.sidebar');
    Route::get('/content/faq', [AdminDashboardController::class, 'contentFaq'])->name('admin.content.faq');
    Route::get('/content/contact-info', [AdminDashboardController::class, 'contentContactInfo'])->name('admin.content.contact-info');
    Route::get('/settings', [AdminDashboardController::class, 'settings'])->name('admin.settings');
});
