<?php

use App\Http\Controllers\Api\Admin\MembershipController as AdminMembershipApiController;
use App\Http\Controllers\Api\Admin\PaymentController as AdminPaymentApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Public webhook endpoints for payment gateways
Route::middleware(['api', 'throttle:payment-webhooks'])->prefix('v1/webhooks')->group(function () {
    Route::post('/mpesa', [\App\Http\Controllers\Api\PaymentWebhookController::class, 'handleMpesa']);
    Route::post('/ecocash', [\App\Http\Controllers\Api\PaymentWebhookController::class, 'handleEcoCash']);
});

Route::middleware(['auth:sanctum', 'admin'])->prefix('v1/admin')->name('api.admin.')->group(function () {

Route::middleware('role:membership_admin,reports_admin,super_admin')
        ->prefix('memberships')
        ->name('memberships.')
        ->group(function () {
            Route::get('/', [AdminMembershipApiController::class, 'index'])->name('index');
            Route::get('/{membership}', [AdminMembershipApiController::class, 'show'])->name('show');
        });

Route::middleware('role:payment_admin,reports_admin,super_admin')
        ->prefix('payments')
        ->name('payments.')
        ->group(function () {
            Route::get('/', [AdminPaymentApiController::class, 'index'])->name('index');
            Route::get('/{payment}', [AdminPaymentApiController::class, 'show'])->name('show');
        });
});