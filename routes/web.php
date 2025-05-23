<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Laravel\Socialite\Facades\Socialite;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

// Traditional authentication routes
require __DIR__.'/auth.php';

// Social Login Routes
Route::middleware('guest')->group(function () {
    // Route to redirect to the provider (e.g., Google, GitHub)
    Route::get('auth/{provider}', [LoginController::class, 'redirectToProvider'])
        ->name('social.login');

    // Callback route that gets called after successful login with the provider
    Route::get('auth/{provider}/callback', [LoginController::class, 'handleProviderCallback'])
        ->name('social.callback');
});

// Email verification and password confirmation routes for authenticated users
Route::middleware('auth')->group(function () {
    Route::view('verify-email', 'livewire.pages.auth.verify-email')
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::view('confirm-password', 'pages.auth.confirm-password')
        ->name('password.confirm');
});
