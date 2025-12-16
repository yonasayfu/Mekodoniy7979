<?php

use App\Http\Controllers\GlobalSearchController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DataExportController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\RoleManagementController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Mailbox\MailpitWebhookController;
use App\Http\Controllers\MailboxController;
use App\Http\Controllers\NotificationPreferenceController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\TwoFactorEmailRecoveryController;
use App\Http\Controllers\PendingApprovalController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\ElderController;
use App\Http\Controllers\PledgeController;
use App\Http\Controllers\VisitController;
use App\Http\Controllers\DonorDashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

Route::get('/', [WelcomeController::class, 'index'])->name('home');

if (! app()->runningInConsole()) {
    Route::impersonate();
}

if (app()->environment('local')) {
    Route::post('mailpit/webhook', MailpitWebhookController::class)
        ->middleware(['mailpit.signature'])
        ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])
        ->name('mailpit.webhook');
}

Route::get('/guest-donation', function () {
    return Inertia::render('GuestDonation');
})->name('guest.donation');

Route::post('/donations/guest', [DonationController::class, 'store'])->name('donations.guest.store');

Route::middleware('auth')->group(function () {
    Route::get('onboarding/pending-approval', PendingApprovalController::class)->name('onboarding.pending');

    Route::middleware(['verified', 'approved'])->group(function () {
        Route::get('global-search', GlobalSearchController::class)->name('global-search');

        // Conditional dashboard route
        Route::get('dashboard', function () {
            if (Auth::user()->hasRole('External')) {
                return redirect()->route('donors.dashboard');
            }
            return app(DashboardController::class)->__invoke(request());
        })->name('dashboard');

        // Donor Dashboard Route
        Route::get('donors/dashboard', [DonorDashboardController::class, 'index'])->name('donors.dashboard');

        Route::get('branches/export', [BranchController::class, 'export'])->name('branches.export');

        Route::resource('branches', BranchController::class)
            ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'])
            ->middleware('permission:branches.manage');

        Route::get('elders/export', [ElderController::class, 'export'])->name('elders.export');

        Route::resource('elders', ElderController::class)
            ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'])
            ->middleware('permission:elders.manage');

        Route::resource('pledges', PledgeController::class)
            ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'])
            ->middleware('permission:pledges.manage');

        Route::resource('visits', VisitController::class)
            ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'])
            ->middleware('permission:visits.manage');

        Route::get('reports', [ReportController::class, 'index'])
            ->name('reports.index')
            ->middleware('permission:reports.view');
        // New route for Impact Book generation
        Route::get('reports/impact-book', [ReportController::class, 'generateImpactBook'])
            ->name('reports.impact-book')
            ->middleware('role:External'); // Only donors can generate their own impact book

        Route::get('/user/two-factor-authentication', function () {
            return Inertia::render('Profile/TwoFactorAuthentication');
        })->name('profile.two-factor-authentication');

        // Notification Preferences Routes
        Route::get('/profile/notification-preferences', [NotificationPreferenceController::class, 'index'])
            ->name('profile.notification-preferences.index');
        Route::post('/profile/notification-preferences', [NotificationPreferenceController::class, 'update'])
            ->name('profile.notification-preferences.update');

        if (app()->environment('local')) {
            Route::get('mailbox', [MailboxController::class, 'index'])
                ->name('mailbox.index')
                ->middleware('permission:mailbox.view');

            Route::get('mailbox/{message}', [MailboxController::class, 'show'])
                ->name('mailbox.show')
                ->middleware('permission:mailbox.view');

            Route::post('mailbox/{message}/process', [MailboxController::class, 'process'])
                ->name('mailbox.process')
                ->middleware('permission:mailbox.process');
        }

        Route::get('exports', [DataExportController::class, 'index'])->name('exports.index');
        Route::get('exports/{export}', [DataExportController::class, 'download'])->name('exports.download');
        Route::delete('exports/{export}', [DataExportController::class, 'destroy'])->name('exports.destroy');

        // Notifications
        Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::get('notifications/unread', [NotificationController::class, 'getUnread'])->name('notifications.unread');
        Route::post('notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
        Route::post('notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.markAllRead');

        Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');

        Route::get('/two-factor-email-recovery/codes', [TwoFactorEmailRecoveryController::class, 'getRecoveryCodes'])->name('two-factor-email-recovery.codes');
        Route::post('/two-factor-email-recovery/send', [TwoFactorEmailRecoveryController::class, 'sendRecoveryCode'])->name('two-factor-email-recovery.send');
        Route::post('/two-factor-email-recovery/verify', [TwoFactorEmailRecoveryController::class, 'verifyRecoveryCode'])->name('two-factor-email-recovery.verify');

        Route::middleware('permission:staff.view')->group(function () {
            Route::get('staff', [StaffController::class, 'index'])->name('staff.index');
            Route::get('staff/export', [StaffController::class, 'export'])->name('staff.export');
        });

        Route::get('staff/create', [StaffController::class, 'create'])
            ->name('staff.create')
            ->middleware('permission:staff.create');

        Route::post('staff', [StaffController::class, 'store'])
            ->name('staff.store')
            ->middleware('permission:staff.create');

        Route::get('staff/{staff}', [StaffController::class, 'show'])
            ->name('staff.show')
            ->middleware('permission:staff.view');

        Route::get('staff/{staff}/edit', [StaffController::class, 'edit'])
            ->name('staff.edit')
            ->middleware('permission:staff.update');

        Route::put('staff/{staff}', [StaffController::class, 'update'])
            ->name('staff.update')
            ->middleware('permission:staff.update');

        Route::delete('staff/{staff}', [StaffController::class, 'destroy'])
            ->name('staff.destroy')
            ->middleware('permission:staff.delete');

        Route::middleware('permission:users.manage')->group(function () {
            Route::get('users/export', [UserManagementController::class, 'export'])->name('users.export');
            Route::get('users', [UserManagementController::class, 'index'])->name('users.index');
            Route::get('users/create', [UserManagementController::class, 'create'])->name('users.create');
            Route::post('users', [UserManagementController::class, 'store'])->name('users.store');
            Route::get('users/{user}', [UserManagementController::class, 'show'])->name('users.show');
            Route::get('users/{user}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
            Route::put('users/{user}', [UserManagementController::class, 'update'])->name('users.update');
            Route::delete('users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');
            Route::post('users/{user}/kick', [UserManagementController::class, 'kickUser'])->name('users.kick');
            Route::post('users/{user}/unkick', [UserManagementController::class, 'unkickUser'])->name('users.unkick');
            Route::post('users/{user}/ban', [UserManagementController::class, 'banUser'])->name('users.ban');
            Route::post('users/{user}/unban', [UserManagementController::class, 'unbanUser'])->name('users.unban');
            Route::post('users/{user}/mute', [UserManagementController::class, 'muteUser'])->name('users.mute');
            Route::post('users/{user}/unmute', [UserManagementController::class, 'unmuteUser'])->name('users.unmute');
            Route::post('users/{user}/warn', [UserManagementController::class, 'issueWarning'])->name('users.warn');
            Route::get('users/{user}/warnings', [UserManagementController::class, 'showWarnings'])->name('users.warnings.index');
        });

        Route::middleware('permission:roles.manage|users.manage')->group(function () {
            Route::resource('roles', RoleManagementController::class)->only([
                'index',
                'create',
                'store',
                'edit',
                'update',
                'destroy',
            ]);
        });

        Route::prefix('samples')->name('samples.')->group(function () {
            Route::get('/', function () {
                return Inertia::render('samples/Index');
            })->name('index');

            Route::get('/admin', function () {
                return Inertia::render('samples/SampleAdminPage');
            })->middleware('role:Admin')->name('admin');

            Route::get('/manager', function () {
                return Inertia::render('samples/SampleManagerPage');
            })->middleware('role:Manager')->name('manager');

            Route::get('/technician', function () {
                return Inertia::render('samples/SampleTechnicianPage');
            })->middleware('role:Technician')->name('technician');

            Route::get('/external', function () {
                return Inertia::render('samples/SampleExternalPage');
            })->middleware('role:External|ReadOnly')->name('external');
        });
    });
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';