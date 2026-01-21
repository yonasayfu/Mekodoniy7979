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
use App\Http\Controllers\DonationReceiptController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\ElderController;
use App\Http\Controllers\ElderLifecycleController;
use App\Http\Controllers\ElderHealthAssessmentController;
use App\Http\Controllers\ElderMedicalConditionController;
use App\Http\Controllers\ElderDocumentController;
use App\Http\Controllers\CaseNoteController;
use App\Http\Controllers\CaseNoteAttachmentController;
use App\Http\Controllers\OutboundMessageController;
use App\Http\Controllers\ElderMedicationController;
use App\Http\Controllers\SponsorshipController;
use App\Http\Controllers\SponsorshipProposalController;
use App\Http\Controllers\VisitController;
use App\Http\Controllers\DonorDashboardController;
use App\Http\Controllers\DonorOnboardingController;
use App\Http\Controllers\AnnualReportController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ComplianceController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\PaymentReconciliationController;
use App\Http\Controllers\Payments\TelebirrWebhookController;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
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
        ->withoutMiddleware([ValidateCsrfToken::class])
        ->name('mailpit.webhook');
}

Route::post('payments/telebirr/webhook', TelebirrWebhookController::class)
    ->middleware(['telebirr.signature'])
    ->withoutMiddleware([ValidateCsrfToken::class])
    ->name('payments.telebirr.webhook');

Route::get('elders/{elder}/public', [ElderController::class, 'publicShow'])
    ->name('elders.public.show');

Route::get('campaign/{campaign:slug}', [CampaignController::class, 'landing'])
    ->name('campaigns.landing');

Route::get('/guest-donation', function () {
    return Inertia::render('GuestDonation');
})->name('guest.donation');

Route::post('/donations/guest', [DonationController::class, 'storeGuest'])->name('donations.guest.store');

Route::post('/donations', [DonationController::class, 'store'])
    ->middleware('auth')
    ->name('donations.store');

Route::post('/pre-sponsorships', [\App\Http\Controllers\PreSponsorshipController::class, 'store'])->name('pre-sponsorships.store');

Route::get('receipts/{receiptUuid}', [DonationReceiptController::class, 'show'])
    ->name('receipts.show');
Route::get('receipts/annual/{year?}', [DonationReceiptController::class, 'annual'])
    ->middleware(['auth', 'verified', 'approved'])
    ->name('receipts.annual');

// Case Notes Routes
Route::middleware(['auth', 'verified', 'approved'])->group(function () {
    Route::resource('elders.case-notes', CaseNoteController::class)
        ->except(['show', 'edit', 'create'])
        ->scoped([
            'case_note' => 'id',
        ])
        ->parameters([
            'case-notes' => 'caseNote',
        ]);

    // Restore soft-deleted case note
    Route::post('elders/{elder}/case-notes/{case_note}/restore', [CaseNoteController::class, 'restore'])
        ->name('elders.case-notes.restore')
        ->withTrashed();
});

Route::middleware('auth')->group(function () {
    Route::get('onboarding/pending-approval', PendingApprovalController::class)->name('onboarding.pending');

    Route::middleware(['verified', 'approved'])->group(function () {
        Route::get('global-search', GlobalSearchController::class)->name('global-search');

        // Conditional dashboard route
        Route::get('dashboard', function () {
            if (Auth::user()->hasAnyRole(['External', 'Donor'])) {
                return redirect()->route('donors.dashboard');
            }
            return app(DashboardController::class)->__invoke(request());
        })->name('dashboard');

        Route::middleware('role:External|Donor')->group(function () {
            Route::get('donors/onboarding', [DonorOnboardingController::class, 'show'])
                ->name('donors.onboarding.show');
            Route::post('donors/onboarding', [DonorOnboardingController::class, 'store'])
                ->name('donors.onboarding.store');

            Route::get('donors/dashboard', [DonorDashboardController::class, 'index'])
                ->name('donors.dashboard');

            Route::get('annual-reports/{annualReport}', [AnnualReportController::class, 'download'])
                ->name('annual-reports.download');
        });

        Route::prefix('compliance')->name('compliance.')->group(function () {
            Route::get('export', [ComplianceController::class, 'exportDonorData'])->name('export');
            Route::post('redact', [ComplianceController::class, 'requestRedaction'])->name('redact');
            Route::post('donations/{donation}/kyc', [ComplianceController::class, 'updateKycStatus'])
                ->middleware('can:donations.manage')
                ->name('donations.kyc.update');
            Route::post('redaction/{user}/finalize', [ComplianceController::class, 'finalizeRedaction'])
                ->middleware('can:users.manage')
                ->name('redaction.finalize');
        });

        Route::get('branches/export', [BranchController::class, 'export'])->name('branches.export');

        Route::resource('branches', BranchController::class)
            ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'])
            ->middleware('can:branches.manage');

        Route::get('elders/export', [ElderController::class, 'export'])->name('elders.export');

        Route::resource('elders', ElderController::class)
            ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'])
            ->middleware('can:elders.manage');

        Route::post('elders/{elder}/documents', [ElderDocumentController::class, 'store'])
            ->name('elders.documents.store')
            ->middleware('can:elders.manage');
        Route::delete('elders/{elder}/documents/{document}', [ElderDocumentController::class, 'destroy'])
            ->name('elders.documents.destroy')
            ->middleware('can:elders.manage');
        Route::get('elders/{elder}/documents/{document}/download', [ElderDocumentController::class, 'download'])
            ->name('elders.documents.download')
            ->middleware('can:elders.manage');

        Route::post('elders/{elder}/lifecycle/status', [ElderLifecycleController::class, 'updateStatus'])
            ->name('elders.lifecycle.status')
            ->middleware('can:elders.manage');

        Route::post('elders/{elder}/health-assessments', [ElderHealthAssessmentController::class, 'store'])
            ->name('elders.health-assessments.store')
            ->middleware('can:elders.manage');
        Route::put('elders/{elder}/health-assessments/{assessment}', [ElderHealthAssessmentController::class, 'update'])
            ->name('elders.health-assessments.update')
            ->middleware('can:elders.manage');
        Route::delete('elders/{elder}/health-assessments/{assessment}', [ElderHealthAssessmentController::class, 'destroy'])
            ->name('elders.health-assessments.destroy')
            ->middleware('can:elders.manage');

        Route::post('elders/{elder}/medical-conditions', [ElderMedicalConditionController::class, 'store'])
            ->name('elders.medical-conditions.store')
            ->middleware('can:elders.manage');
        Route::put('elders/{elder}/medical-conditions/{condition}', [ElderMedicalConditionController::class, 'update'])
            ->name('elders.medical-conditions.update')
            ->middleware('can:elders.manage');
        Route::delete('elders/{elder}/medical-conditions/{condition}', [ElderMedicalConditionController::class, 'destroy'])
            ->name('elders.medical-conditions.destroy')
            ->middleware('can:elders.manage');

        Route::post('elders/{elder}/medications', [ElderMedicationController::class, 'store'])
            ->name('elders.medications.store')
            ->middleware('can:elders.manage');
        Route::put('elders/{elder}/medications/{medication}', [ElderMedicationController::class, 'update'])
            ->name('elders.medications.update')
            ->middleware('can:elders.manage');
        Route::delete('elders/{elder}/medications/{medication}', [ElderMedicationController::class, 'destroy'])
            ->name('elders.medications.destroy')
            ->middleware('can:elders.manage');

        // Case Notes Routes
        Route::get('elders/{elder}/case-notes', [CaseNoteController::class, 'index'])
            ->name('elders.case-notes.index')
            ->middleware('can:case_notes.view');

        Route::post('elders/{elder}/case-notes', [CaseNoteController::class, 'store'])
            ->name('elders.case-notes.store')
            ->middleware('can:case_notes.manage');

        Route::put('elders/{elder}/case-notes/{case_note}', [CaseNoteController::class, 'update'])
            ->name('elders.case-notes.update')
            ->middleware('can:case_notes.manage');

        Route::delete('elders/{elder}/case-notes/{case_note}', [CaseNoteController::class, 'destroy'])
            ->name('elders.case-notes.destroy')
            ->middleware('can:case_notes.delete');

        Route::put('elders/{elder}/case-notes/{case_note}/restore', [CaseNoteController::class, 'restore'])
            ->name('elders.case-notes.restore')
            ->middleware('can:case_notes.manage');

        Route::get('case-notes/{case_note}/attachments/{attachment}', [CaseNoteAttachmentController::class, 'download'])
            ->name('case-notes.attachments.download')
            ->middleware('can:case_notes.view');
        Route::delete('case-notes/{case_note}/attachments/{attachment}', [CaseNoteAttachmentController::class, 'destroy'])
            ->name('case-notes.attachments.destroy')
            ->middleware('can:case_notes.manage');

        Route::get('sponsorships/export', [SponsorshipController::class, 'export'])->name('sponsorships.export');

        Route::post('sponsorships/{sponsorship}/unmatch', [SponsorshipController::class, 'unmatch'])
            ->name('sponsorships.unmatch')
            ->middleware('can:sponsorships.manage');

        Route::resource('sponsorships', SponsorshipController::class)
            ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'])
            ->middleware('can:sponsorships.manage');

        Route::post('elders/{elder}/proposals', [SponsorshipProposalController::class, 'store'])
            ->name('elders.proposals.store')
            ->middleware('can:sponsorships.manage');
        Route::delete('elders/{elder}/proposals/{proposal}', [SponsorshipProposalController::class, 'cancel'])
            ->name('elders.proposals.cancel')
            ->middleware('can:sponsorships.manage');

        Route::post('proposals/{proposal}/accept', [SponsorshipProposalController::class, 'accept'])
            ->name('proposals.accept')
            ->middleware('role:External|Donor');
        Route::post('proposals/{proposal}/decline', [SponsorshipProposalController::class, 'decline'])
            ->name('proposals.decline')
            ->middleware('role:External|Donor');

        Route::get('visits/export', [VisitController::class, 'export'])->name('visits.export');

        Route::resource('visits', VisitController::class)
            ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'])
            ->middleware('can:visits.manage');

        Route::middleware('can:donations.manage')->group(function () {
            Route::get('reconciliation', [PaymentReconciliationController::class, 'index'])
                ->name('reconciliation.index');
            Route::post('reconciliation', [PaymentReconciliationController::class, 'store'])
                ->name('reconciliation.store');
            Route::get('reconciliation/{import}', [PaymentReconciliationController::class, 'show'])
                ->name('reconciliation.show');
            Route::post('reconciliation/{import}/items/{item}/match', [PaymentReconciliationController::class, 'matchItem'])
                ->name('reconciliation.items.match');
            Route::post('reconciliation/{import}/items/{item}/ignore', [PaymentReconciliationController::class, 'ignoreItem'])
                ->name('reconciliation.items.ignore');
        });

        Route::resource('campaigns', CampaignController::class)
            ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'])
            ->middleware('can:campaigns.manage');

        Route::get('reports', [ReportController::class, 'index'])
            ->name('reports.index')
            ->middleware('can:reports.view');

        Route::get('reports/donations', [ReportController::class, 'donationsReport'])
            ->name('reports.donations')
            ->middleware('can:reports.view');

        // Detailed report routes for dashboard drill-down
        Route::get('reports/detailed', [ReportController::class, 'detailedReport'])
            ->name('reports.detailed')
            ->middleware('can:reports.view');

        Route::get('reports/activity', [ReportController::class, 'activityReport'])
            ->name('reports.activity')
            ->middleware('can:reports.view');

        Route::get('reports/export', [ReportController::class, 'exportReport'])
            ->name('reports.export')
            ->middleware('can:reports.view');

        Route::get('outbound', [OutboundMessageController::class, 'index'])
            ->name('outbound.index')
            ->middleware('can:notifications.view');

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
                ->middleware('can:mailbox.view');

            Route::get('mailbox/{message}', [MailboxController::class, 'show'])
                ->name('mailbox.show')
                ->middleware('can:mailbox.view');

            Route::post('mailbox/{message}/process', [MailboxController::class, 'process'])
                ->name('mailbox.process')
                ->middleware('can:mailbox.process');
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
        Route::get('activity-logs/export', [ActivityLogController::class, 'export'])->name('activity-logs.export');

        Route::get('/two-factor-email-recovery/codes', [TwoFactorEmailRecoveryController::class, 'getRecoveryCodes'])->name('two-factor-email-recovery.codes');
        Route::post('/two-factor-email-recovery/send', [TwoFactorEmailRecoveryController::class, 'sendRecoveryCode'])->name('two-factor-email-recovery.send');
        Route::post('/two-factor-email-recovery/verify', [TwoFactorEmailRecoveryController::class, 'verifyRecoveryCode'])->name('two-factor-email-recovery.verify');

        Route::middleware('can:staff.view')->group(function () {
            Route::get('staff', [StaffController::class, 'index'])->name('staff.index');
            Route::get('staff/export', [StaffController::class, 'export'])->name('staff.export');
        });

        Route::get('staff/create', [StaffController::class, 'create'])
            ->name('staff.create')
            ->middleware('can:staff.create');

        Route::post('staff', [StaffController::class, 'store'])
            ->name('staff.store')
            ->middleware('can:staff.create');

        Route::get('staff/{staff}', [StaffController::class, 'show'])
            ->name('staff.show')
            ->middleware('can:staff.view');

        Route::get('staff/{staff}/edit', [StaffController::class, 'edit'])
            ->name('staff.edit')
            ->middleware('can:staff.update');

        Route::put('staff/{staff}', [StaffController::class, 'update'])
            ->name('staff.update')
            ->middleware('can:staff.update');

        Route::delete('staff/{staff}', [StaffController::class, 'destroy'])
            ->name('staff.destroy')
            ->middleware('can:staff.delete');

        Route::middleware('can:users.manage')->group(function () {
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

        Route::middleware('can:users.manage')->group(function () {
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
