<?php

use Illuminate\Support\Facades\Route;
use Jexactyl\Http\Controllers\Api\Webhook\PaymentNotificationController;
use Jexactyl\Http\Controllers\Api\Webhook\PaymentWebhookController;
use Jexactyl\Http\Controllers\Base;
use Jexactyl\Http\Middleware\RequireTwoFactorAuthentication;

Route::get('/', [Base\IndexController::class, 'index'])->name('index')->fallback();
Route::get('/account', [Base\IndexController::class, 'index'])
    ->withoutMiddleware(RequireTwoFactorAuthentication::class)
    ->name('account');

Route::get('/locales/locale.json', Base\LocaleController::class)
    ->withoutMiddleware(['auth', RequireTwoFactorAuthentication::class])
    ->where('namespace', '.*');

Route::get('/{react}', [Base\IndexController::class, 'index'])
    ->where('react', '^(?!(\/)?(api|auth|admin|daemon)).+');

Route::group(['prefix' => '/webhook', 'middleware' => 'throttle:5,1'], function () {
    Route::post('/yookassa', [PaymentWebhookController::class, 'yookassa'])->name('api:webhook:yookassa');
});
