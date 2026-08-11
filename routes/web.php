<?php

use App\Http\Controllers\Admin\AgmNoticeController;
use App\Http\Controllers\Admin\ContentAdminController;
use App\Http\Controllers\Admin\DocumentReviewController;
use App\Http\Controllers\Admin\EcMinutesController;
use App\Http\Controllers\Admin\MembershipAdminController;
use App\Http\Controllers\Admin\PaymentAdminController;
use App\Http\Controllers\Admin\ReportsController;
use App\Http\Controllers\Admin\ResignationAdminController;
use App\Http\Controllers\Admin\SuperAdminController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MemberDashboardController;
use App\Http\Controllers\MemberProfileController;
use App\Http\Controllers\MemberServicesController;
use App\Http\Controllers\ResignationController as MemberResignationController;
use App\Livewire\Membership\ApplicationForm;
use App\Livewire\Payment\InitiatePayment;
use App\Models\MembershipCategory;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
    Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [RegisterController::class, 'register']);
    Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');
});

Route::post('logout', [LogoutController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('email/verify', [VerificationController::class, 'show'])->name('verification.notice');
    Route::get('email/verify/{id}/{hash}', [VerifyEmailController::class, 'verify'])
        ->name('verification.verify');
    Route::post('email/verification-notification', [VerificationController::class, 'resend'])
        ->name('verification.resend');
});

Route::middleware('auth')->group(function () {

    Route::get('/member/dashboard', [MemberDashboardController::class, 'index'])
        ->name('member.dashboard');

    Route::get('/member/profile', [MemberProfileController::class, 'edit'])->name('member.profile');
    Route::put('/member/profile', [MemberProfileController::class, 'update'])->name('member.profile.update')
        ->middleware('throttle:10,1');
    Route::put('/member/profile/password', [MemberProfileController::class, 'updatePassword'])->name('member.profile.password')
        ->middleware('throttle:5,1');

    Route::get('/membership/apply', ApplicationForm::class)
        ->middleware(['verified', 'throttle:5,1'])
        ->name('membership.apply');

    Route::get('/payment/initiate', InitiatePayment::class)
        ->middleware(['verified', 'throttle:10,1'])
        ->name('payment.initiate');

    Route::prefix('documents')->name('documents.')->group(function () {
        Route::get('/certificate/{membership}', [DocumentController::class, 'certificate'])->name('certificate');
        Route::get('/receipt/{payment}', [DocumentController::class, 'receipt'])->name('receipt');
        Route::get('/welcome-pack/{membership}', [DocumentController::class, 'welcomePack'])->name('welcome-pack');
    });

    Route::get('/member/resign', [MemberResignationController::class, 'create'])
        ->name('member.resign.create');
    Route::post('/member/resign', [MemberResignationController::class, 'store'])
        ->name('member.resign.store');

    Route::get('/services/minutes', [MemberServicesController::class, 'minutes'])->name('member.services.minutes');
    Route::get('/services/events', [MemberServicesController::class, 'events'])->name('member.services.events');
    Route::get('/services/jobs', [MemberServicesController::class, 'jobs'])->name('member.services.jobs');
    Route::get('/services/scholarships', [MemberServicesController::class, 'scholarships'])->name('member.services.scholarships');
    Route::get('/services/internships', [MemberServicesController::class, 'internships'])->name('member.services.internships');
    Route::get('/services/redirect/{type}/{id}', [MemberServicesController::class, 'redirect'])->name('member.services.redirect');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::middleware('super-admin')->group(function () {
        Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/admins', [SuperAdminController::class, 'listAdmins'])->name('admins.list');
        Route::get('/admins/{user}', [SuperAdminController::class, 'showAdmin'])->name('admins.show');
        Route::post('/admins/create', [SuperAdminController::class, 'createAdmin'])->name('admins.create');
        Route::put('/admins/{user}/roles', [SuperAdminController::class, 'updateAdminRoles'])->name('admins.roles.update');
        Route::post('/admins/{user}/deactivate', [SuperAdminController::class, 'deactivateAdmin'])->name('admins.deactivate');
        Route::get('/audit-log', [SuperAdminController::class, 'auditLog'])->name('audit-log');
        Route::get('/roles', [SuperAdminController::class, 'manageRoles'])->name('roles.manage');
    });

    Route::middleware('role:membership_admin,super_admin')
        ->prefix('memberships')
        ->name('memberships.')
        ->group(function () {
            Route::get('/pending', [MembershipAdminController::class, 'index'])->name('index');
            Route::get('/list/all', [MembershipAdminController::class, 'listMembers'])->name('list');
            Route::get('/list/rejected', [MembershipAdminController::class, 'listRejected'])->name('rejected');
            Route::post('/bulk-approve', [MembershipAdminController::class, 'bulkApprove'])->name('bulk.approve');
            Route::post('/bulk-reject', [MembershipAdminController::class, 'bulkReject'])->name('bulk.reject');
            Route::get('/export', [MembershipAdminController::class, 'export'])->name('export');

            Route::get('/{membership}', [MembershipAdminController::class, 'show'])->whereNumber('membership')->name('show');
            Route::put('/{membership}/email', [MembershipAdminController::class, 'updateMemberEmail'])->whereNumber('membership')->name('email.update');
            Route::post('/{membership}/approve', [MembershipAdminController::class, 'approve'])->whereNumber('membership')->name('approve');
            Route::post('/{membership}/reject', [MembershipAdminController::class, 'reject'])->whereNumber('membership')->name('reject');

            Route::post('/{membership}/documents/{document}/review',
                [MembershipAdminController::class, 'reviewDocument'])->whereNumber(['membership', 'document'])->name('document.review');

            Route::post('/{membership}/status', [MembershipAdminController::class, 'updateStatus'])
                ->whereNumber('membership')
                ->name('status.update');
        });

    Route::middleware('role:finance_admin,super_admin,content_admin')->group(function () {
        Route::get('/memberships/categories', [MembershipAdminController::class, 'categories'])->name('memberships.categories.index');
        Route::get('/memberships/categories/{category}/edit', [MembershipAdminController::class, 'editCategory'])->name('memberships.categories.edit');
        Route::put('/memberships/categories/{category}', [MembershipAdminController::class, 'updateCategory'])->name('memberships.categories.update');
    });

    Route::middleware('role:membership_admin,super_admin')
        ->prefix('resignations')
        ->name('resignations.')
        ->group(function () {
            Route::get('/', [ResignationAdminController::class, 'index'])->name('index');
            Route::get('/{resignation}', [ResignationAdminController::class, 'show'])->name('show');
            Route::post('/{resignation}/acknowledge', [ResignationAdminController::class, 'acknowledge'])->name('acknowledge');
            Route::post('/{resignation}/reject', [ResignationAdminController::class, 'reject'])->name('reject');
        });

    Route::middleware('role:payment_admin,super_admin')
        ->prefix('payments')
        ->name('payments.')
        ->group(function () {
            Route::get('/pending', [PaymentAdminController::class, 'index'])->name('index');
            Route::get('/list/verified', [PaymentAdminController::class, 'verified'])->name('verified');
            Route::get('/list/rejected', [PaymentAdminController::class, 'rejected'])->name('rejected');
            Route::get('/{payment}', [PaymentAdminController::class, 'show'])->name('show');
            Route::post('/{payment}/verify', [PaymentAdminController::class, 'verify'])->name('verify');
            Route::post('/{payment}/reject', [PaymentAdminController::class, 'reject'])->name('reject');
            Route::get('/{payment}/receipt', [PaymentAdminController::class, 'receipt'])->name('receipt');
        });

    Route::middleware('role:reports_admin,super_admin')
        ->prefix('reports')
        ->name('reports.')
        ->group(function () {
            Route::get('/', [ReportsController::class, 'index'])->name('index');
            Route::get('/memberships', [ReportsController::class, 'membershipReport'])->name('memberships');
            Route::get('/payments', [ReportsController::class, 'paymentReport'])->name('payments');
            Route::get('/export/members', [ReportsController::class, 'exportMembers'])->name('export.members');
            Route::get('/export/payments', [ReportsController::class, 'exportPayments'])->name('export.payments');
        });

    Route::middleware('role:super_admin,content_admin')
        ->prefix('documents')
        ->name('documents.')
        ->group(function () {
            Route::get('/certificate/{membership}', [DocumentController::class, 'certificate'])->name('certificate');
            Route::get('/receipt/{payment}', [DocumentController::class, 'receipt'])->name('receipt');
            Route::get('/welcome-pack/{membership}', [DocumentController::class, 'welcomePack'])->name('welcome-pack');
            Route::get('/', [DocumentReviewController::class, 'queue'])->name('queue');
            Route::get('/compose/agm-notice', [AgmNoticeController::class, 'create'])->name('compose.agm');
            Route::post('/compose/agm-notice', [AgmNoticeController::class, 'store'])->name('store.agm');
            Route::get('/compose/ec-minutes', [EcMinutesController::class, 'create'])->name('compose.minutes');
            Route::post('/compose/ec-minutes', [EcMinutesController::class, 'store'])->name('store.minutes');
            Route::post('/preview-draft', [DocumentReviewController::class, 'previewDraft'])->name('preview-draft');
            Route::get('/{review}', [DocumentReviewController::class, 'show'])->name('show');
            Route::put('/{review}', [DocumentReviewController::class, 'update'])->name('update');
            Route::get('/{review}/preview', [DocumentReviewController::class, 'preview'])->name('preview');
            Route::post('/{review}/approve', [DocumentReviewController::class, 'approve'])->name('approve');
            Route::post('/{review}/send', [DocumentReviewController::class, 'send'])->name('send');
            Route::post('/{review}/cancel', [DocumentReviewController::class, 'cancel'])->name('cancel');
        });

    Route::middleware('role:content_admin,super_admin')->group(function () {
        Route::get('/content-dashboard', [ContentAdminController::class, 'dashboard'])->name('content.dashboard');
    });

    Route::middleware('role:content_admin,super_admin')
        ->prefix('services')
        ->name('services.')
        ->group(function () {
            Route::get('/{type}', [\App\Http\Controllers\Admin\ServicesController::class, 'index'])->name('index');
            Route::get('/{type}/create', [\App\Http\Controllers\Admin\ServicesController::class, 'create'])->name('create');
            Route::post('/{type}', [\App\Http\Controllers\Admin\ServicesController::class, 'store'])->name('store');
            Route::get('/{type}/{id}/edit', [\App\Http\Controllers\Admin\ServicesController::class, 'edit'])->name('edit');
            Route::put('/{type}/{id}', [\App\Http\Controllers\Admin\ServicesController::class, 'update'])->name('update');
            Route::delete('/{type}/{id}', [\App\Http\Controllers\Admin\ServicesController::class, 'destroy'])->name('destroy');
        });
});
