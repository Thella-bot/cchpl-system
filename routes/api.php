<?php

use App\Http\Controllers\Api\Admin\MembershipController as AdminMembershipApiController;
use App\Http\Controllers\Api\Admin\PaymentController as AdminPaymentApiController;
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


// =============================================================================
// Admin API Endpoints
// =============================================================================
Route::middleware(['auth:sanctum', 'admin'])->prefix('v1/admin')->name('api.admin.')->group(function () {

    // Membership API routes
    Route::middleware('role:membership_admin,reports_admin,super_admin')
        ->prefix('memberships')
        ->name('memberships.')
        ->group(function () {
            Route::get('/', [AdminMembershipApiController::class, 'index'])->name('index');
            Route::get('/{membership}', [AdminMembershipApiController::class, 'show'])->name('show');
        });

    // Payment API routes
    Route::middleware('role:payment_admin,reports_admin,super_admin')
        ->prefix('payments')
        ->name('payments.')
        ->group(function () {
            Route::get('/', [AdminPaymentApiController::class, 'index'])->name('index');
            Route::get('/{payment}', [AdminPaymentApiController::class, 'show'])->name('show');
        });
});