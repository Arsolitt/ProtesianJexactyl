<?php

use Illuminate\Support\Facades\Route;
use Jexactyl\Http\Controllers\Api\Webhook\WebhookController;
use Jexactyl\Http\Controllers\Base;
use Jexactyl\Http\Middleware\RequireTwoFactorAuthentication;

Route::get('/', [Base\IndexController::class, 'welcome'])->name('index')->fallback();

Route::get('/information/tos', [Base\IndexController::class, 'tos'])->name('tos');
Route::get('/information/privacy', [Base\IndexController::class, 'privacy'])->name('privacy');
Route::get('/information/contacts', [Base\IndexController::class, 'contacts'])->name('contacts');

Route::get('/account', [Base\IndexController::class, 'index'])
    ->withoutMiddleware(RequireTwoFactorAuthentication::class)
    ->name('account');

Route::get('/register', [Base\OldReferralController::class, 'redirect']);

Route::get('/locales/locale.json', Base\LocaleController::class)
    ->withoutMiddleware(['auth', RequireTwoFactorAuthentication::class])
    ->where('namespace', '.*');

Route::get('/{react}', [Base\IndexController::class, 'index'])
    ->where('react', '^(?!(\/)?(api|auth|admin|daemon)).+');

Route::group(['prefix' => '/webhook', 'middleware' => 'throttle:5,1'], function () {
    Route::post('/payment/{gateway}', [WebhookController::class, 'payment'])->name('api:webhook:payment');
});
