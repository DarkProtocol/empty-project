<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Service - API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for this service.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::prefix('auth')->group(function () {

    Route::middleware('not-auth')->group(function () {
        Route::post('/register', 'RegisterController');
        Route::post('/register/resend-confirmation-email', 'ResendConfirmationEmailController')
            ->middleware('throttle.input:3,15,email');
        Route::post('/register/activate', 'UserActivateController');
        Route::post('/password-reset', 'PasswordResetController');
        Route::post('/password-reset/complete', 'PasswordResetCompleteController');
        Route::post('/login', 'LoginController')
            ->middleware('throttle.input:10,1,email')->name('login');
        Route::post('/token/refresh', 'TokenRefreshController')
            ->name('auth.token.refresh');
    });

    Route::middleware('auth.optional')->group(function () {
        Route::post('/logout', 'LogoutController'); // optional for deleting cookies
        Route::get('/login-as', 'LoginAsController')->name('auth.login-as');
    });

    Route::middleware('auth')->group(function () {
        Route::get('/user', 'UserController');
        // TODO user endpoint
        // TODO ban
    });
});
