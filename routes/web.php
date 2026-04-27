<?php

use App\Http\Controllers\Admin\AgmNoticeController;
use App\Http\Controllers\Admin\DocumentReviewController;
use App\Http\Controllers\Admin\EcMinutesController;
use App\Http\Controllers\Admin\MembershipAdminController;
use App\Http\Controllers\Admin\PaymentAdminController;
use App\Http\Controllers\Admin\ReportsController;
use App\Http\Controllers\Admin\SuperAdminController;
use App\Http\Controllers\Admin\ResignationAdminController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\MemberDashboardController;
use App\Http\Controllers\ResignationController as MemberResignationController;
use App\Http\Controllers\MemberProfileController;
use App\Livewire\Membership\ApplicationForm;
use App\Livewire\Payment\InitiatePayment;
use App\Models\MembershipCategory;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Public website landing page
Route::get('/', function () {
    return view('welcome', [
        'categories' => MembershipCategory::query()
            ->where('name', '!=', 'Honorary')
            ->orderBy('annual_fee')
            ->get(),
    ]);
});

// Authentication routes
Auth::routes(['verify' => true]);

// ─────────────────────────────────────────────────────────────────────────────
// Member-facing routes
// ─────────────────────────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    // Dashboard — the HOME redirect target (see RouteServiceProvider)
    Route::get('/member/dashboard', [MemberDashboardController::class, 'index'])
        ->name('member.dashboard');

    // Profile — throttled to prevent enumeration
    Route::get('/member/profile',          [MemberProfileController::class, 'edit'])->name('member.profile');
    Route::put('/member/profile',          [MemberProfileController::class, 'update'])->name('member.profile.update')
        ->middleware('throttle:10,1');
    Route::put('/member/profile/password', [MemberProfileController::class, 'updatePassword'])->name('member.profile.password')
        ->middleware('throttle:5,1');

    // Membership application — requires verified email + rate limiting
    Route::get('/membership/apply', ApplicationForm::class)
        ->middleware(['verified', 'throttle:5,1'])
        ->name('membership.apply');

    // Payment — requires verified email + rate limiting
    Route::get('/payment/initiate', InitiatePayment::class)
        ->middleware(['verified', 'throttle:10,1'])
        ->name('payment.initiate');

    // Document downloads — members can only download their own (enforced in controller)
    Route::get('/documents/certificate/{membership}',  [DocumentController::class, 'certificate'])
        ->name('documents.certificate');
    Route::get('/documents/receipt/{payment}',         [DocumentController::class, 'receipt'])
        ->name('documents.receipt');
    Route::get('/documents/welcome-pack/{membership}', [DocumentController::class, 'welcomePack'])
        ->name('documents.welcome-pack');

    // Resignation - member submits their own
    Route::get('/member/resign', [MemberResignationController::class, 'create'])
        ->name('member.resign.create');
    Route::post('/member/resign', [MemberResignationController::class, 'store'])
        ->name('member.resign.store');
});

// ─────────────────────────────────────────────────────────────────────────────
// Admin routes
// ─────────────────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    // ── Super Admin ──────────────────────────────────────────────────────────
    Route::middleware('super-admin')->group(function () {
        Route::get('/dashboard',                 [SuperAdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/admins',                    [SuperAdminController::class, 'listAdmins'])->name('admins.list');
        Route::get('/admins/{user}',             [SuperAdminController::class, 'showAdmin'])->name('admins.show');
        Route::post('/admins/create',            [SuperAdminController::class, 'createAdmin'])->name('admins.create');
        Route::put('/admins/{user}/roles',       [SuperAdminController::class, 'updateAdminRoles'])->name('admins.roles.update');
        Route::post('/admins/{user}/deactivate', [SuperAdminController::class, 'deactivateAdmin'])->name('admins.deactivate');
        Route::get('/audit-log',                 [SuperAdminController::class, 'auditLog'])->name('audit-log');
        Route::get('/roles',                     [SuperAdminController::class, 'manageRoles'])->name('roles.manage');
    });

    // ── Membership Admin ─────────────────────────────────────────────────────
    Route::middleware('role:membership_admin,super_admin')
        ->prefix('memberships')
        ->name('memberships.')
        ->group(function () {
            Route::get('/pending',       [MembershipAdminController::class, 'index'])->name('index');
            Route::get('/list/all',      [MembershipAdminController::class, 'listMembers'])->name('list');
            Route::get('/list/rejected', [MembershipAdminController::class, 'listRejected'])->name('rejected');
            Route::post('/bulk',         [MembershipAdminController::class, 'bulkAction'])->name('bulk');
            Route::get('/export',        [MembershipAdminController::class, 'export'])->name('export');

            Route::get('/{membership}',          [MembershipAdminController::class, 'show'])->name('show');
            Route::post('/{membership}/approve', [MembershipAdminController::class, 'approve'])->name('approve');
            Route::post('/{membership}/reject',  [MembershipAdminController::class, 'reject'])->name('reject');

            Route::post('/{membership}/documents/{document}/review',
                [MembershipAdminController::class, 'reviewDocument'])->name('document.review');
        });

    // Finance admin only
    Route::middleware('role:finance_admin,super_admin')->group(function () {
        Route::get('/memberships/categories',                  [MembershipAdminController::class, 'categories'])->name('memberships.categories.index');
        Route::get('/memberships/categories/{category}/edit', [MembershipAdminController::class, 'editCategory'])->name('memberships.categories.edit');
        Route::put('/memberships/categories/{category}',       [MembershipAdminController::class, 'updateCategory'])->name('memberships.categories.update');
    });

    // ── Resignations Admin ───────────────────────────────────────────────────
    Route::middleware('role:membership_admin,super_admin')
        ->prefix('resignations')
        ->name('resignations.')
        ->group(function () {
            Route::get('/', [ResignationAdminController::class, 'index'])->name('index');
            Route::get('/{resignation}', [ResignationAdminController::class, 'show'])->name('show');
            Route::post('/{resignation}/acknowledge', [ResignationAdminController::class, 'acknowledge'])->name('acknowledge');
        });

    // ── Payment Admin ─────────────────────────────────────────────────────────
    Route::middleware('role:payment_admin,super_admin')
        ->prefix('payments')
        ->name('payments.')
        ->group(function () {
            Route::get('/pending',           [PaymentAdminController::class, 'index'])->name('index');
            Route::get('/list/verified',     [PaymentAdminController::class, 'verified'])->name('verified');
            Route::get('/list/rejected',     [PaymentAdminController::class, 'rejected'])->name('rejected');
            Route::get('/{payment}',         [PaymentAdminController::class, 'show'])->name('show');
            Route::post('/{payment}/verify', [PaymentAdminController::class, 'verify'])->name('verify');
            Route::post('/{payment}/reject', [PaymentAdminController::class, 'reject'])->name('reject');
            Route::get('/{payment}/receipt', [PaymentAdminController::class, 'receipt'])->name('receipt');
        });

    // ── Reports Admin ─────────────────────────────────────────────────────────
    Route::middleware('role:reports_admin,super_admin')
        ->prefix('reports')
        ->name('reports.')
        ->group(function () {
            Route::get('/',                [ReportsController::class, 'index'])->name('index');
            Route::get('/memberships',     [ReportsController::class, 'membershipReport'])->name('memberships');
            Route::get('/payments',        [ReportsController::class, 'paymentReport'])->name('payments');
            Route::get('/export/members',  [ReportsController::class, 'exportMembers'])->name('export.members');
            Route::get('/export/payments', [ReportsController::class, 'exportPayments'])->name('export.payments');
        });

    // ── Document Review Queue ─────────────────────────────────────────────────
    Route::middleware('role:super_admin,membership_admin,payment_admin')
        ->prefix('documents')
        ->name('documents.')
        ->group(function () {
            Route::get('/',                    [DocumentReviewController::class, 'queue'])->name('queue');
            Route::get('/compose/agm-notice',  [AgmNoticeController::class, 'create'])->name('compose.agm');
            Route::post('/compose/agm-notice', [AgmNoticeController::class, 'store'])->name('store.agm');
            Route::get('/compose/ec-minutes',  [EcMinutesController::class, 'create'])->name('compose.minutes');
            Route::post('/compose/ec-minutes', [EcMinutesController::class, 'store'])->name('store.minutes');
            Route::post('/preview-draft',      [DocumentReviewController::class, 'previewDraft'])->name('preview-draft');
            Route::get('/{review}',            [DocumentReviewController::class, 'show'])->name('show');
            Route::put('/{review}',            [DocumentReviewController::class, 'update'])->name('update');
            Route::get('/{review}/preview',    [DocumentReviewController::class, 'preview'])->name('preview');
            Route::post('/{review}/approve',   [DocumentReviewController::class, 'approve'])->name('approve');
            Route::post('/{review}/send',      [DocumentReviewController::class, 'send'])->name('send');
            Route::post('/{review}/cancel',    [DocumentReviewController::class, 'cancel'])->name('cancel');
        });
});
