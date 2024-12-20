<?php

use Illuminate\Support\Facades\Route;
<<<<<<< HEAD

Route::view('/', 'welcome');

=======
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\VerifyEmailController;

// Home route
Route::view('/', 'welcome');

// Dashboard and profile routes with middleware
>>>>>>> 50fb32b4a407c00bd39cc6e2dac8d4ff35c85b7e
Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

<<<<<<< HEAD
require __DIR__.'/auth.php';
=======
// Traditional authentication routes
require __DIR__.'/auth.php';

// Social Login Routes
Route::middleware('guest')->group(function () {
    Route::get('auth/{provider}', [LoginController::class, 'redirectToProvider'])
        ->name('social.login');
    Route::get('auth/{provider}/callback', [LoginController::class, 'handleProviderCallback'])
        ->name('social.callback');
});

Route::middleware('auth')->group(function () {
    Route::view('verify-email', 'livewire.pages.auth.verify-email')
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::view('confirm-password', 'pages.auth.confirm-password')
        ->name('password.confirm');
});
>>>>>>> 50fb32b4a407c00bd39cc6e2dac8d4ff35c85b7e
