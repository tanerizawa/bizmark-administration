<?php

use App\Modules\Email\Controllers\Admin\EmailAccountController;
use App\Modules\Email\Controllers\Admin\EmailAssignmentController;
use App\Modules\Email\Controllers\Admin\EmailCampaignController;
use App\Modules\Email\Controllers\Admin\EmailInboxController;
use App\Modules\Email\Controllers\Admin\EmailManagementController;
use App\Modules\Email\Controllers\Admin\EmailSettingsController;
use App\Modules\Email\Controllers\Admin\EmailSubscriberController;
use App\Modules\Email\Controllers\Admin\EmailTemplateController;
use Illuminate\Support\Facades\Route;

Route::name('admin.')->middleware(['web', 'auth', 'email.access'])->prefix('admin')->group(function () {
    // Email Management Hub (Unified Tab Interface)
    Route::get('email-management', [EmailManagementController::class, 'index'])
        ->middleware('permission:email.view_inbox')
        ->name('email-management.index');

    // Email Campaigns
    Route::resource('campaigns', EmailCampaignController::class)
        ->middleware('permission:email.manage_campaigns');
    Route::get('campaigns/{id}/send', [EmailCampaignController::class, 'send'])
        ->middleware('permission:email.manage_campaigns')
        ->name('campaigns.send');
    Route::post('campaigns/{id}/process-send', [EmailCampaignController::class, 'processSend'])
        ->middleware('permission:email.manage_campaigns')
        ->name('campaigns.process-send');
    Route::post('campaigns/{id}/cancel', [EmailCampaignController::class, 'cancel'])
        ->middleware('permission:email.manage_campaigns')
        ->name('campaigns.cancel');
    Route::get('campaigns/{id}/export', [EmailCampaignController::class, 'export'])
        ->middleware('permission:email.manage_campaigns')
        ->name('campaigns.export');

    // Email Inbox
    Route::get('inbox', [EmailInboxController::class, 'index'])
        ->middleware('permission:email.view_inbox')
        ->name('inbox.index');
    Route::get('inbox/compose', [EmailInboxController::class, 'compose'])
        ->middleware('permission:email.send_email')
        ->name('inbox.compose');
    Route::post('inbox/send', [EmailInboxController::class, 'send'])
        ->middleware('permission:email.send_email')
        ->name('inbox.send');
    Route::post('inbox/generate', [EmailInboxController::class, 'generate'])
        ->middleware('permission:email.send_email')
        ->name('inbox.generate');
    Route::delete('inbox/batch-delete', [EmailInboxController::class, 'batchDelete'])
        ->middleware('permission:email.manage')
        ->name('inbox.batch-delete');
    Route::get('inbox/{id}', [EmailInboxController::class, 'show'])
        ->middleware('permission:email.view_inbox')
        ->name('inbox.show');
    Route::get('inbox/{id}/reply', [EmailInboxController::class, 'reply'])
        ->middleware('permission:email.send_email')
        ->name('inbox.reply');
    Route::post('inbox/{id}/reply', [EmailInboxController::class, 'sendReply'])
        ->middleware('permission:email.send_email')
        ->name('inbox.send-reply');
    Route::post('inbox/{id}/read', [EmailInboxController::class, 'markAsRead'])
        ->middleware('permission:email.view_inbox')
        ->name('inbox.mark-read');
    Route::post('inbox/{id}/unread', [EmailInboxController::class, 'markAsUnread'])
        ->middleware('permission:email.view_inbox')
        ->name('inbox.mark-unread');
    Route::post('inbox/{id}/star', [EmailInboxController::class, 'toggleStar'])
        ->middleware('permission:email.view_inbox')
        ->name('inbox.toggle-star');
    Route::post('inbox/{id}/trash', [EmailInboxController::class, 'moveToTrash'])
        ->middleware('permission:email.manage')
        ->name('inbox.trash');
    Route::delete('inbox/{id}', [EmailInboxController::class, 'delete'])
        ->middleware('permission:email.manage')
        ->name('inbox.delete');
    Route::post('inbox/empty-trash', [EmailInboxController::class, 'emptyTrash'])
        ->middleware('permission:email.manage')
        ->name('inbox.empty-trash');

    // Email Subscribers
    Route::resource('subscribers', EmailSubscriberController::class)
        ->middleware('permission:email.manage_subscribers');

    // Email Templates
    Route::resource('templates', EmailTemplateController::class)
        ->middleware('permission:email.manage_templates');

    // Email Settings
    Route::get('email/settings', [EmailSettingsController::class, 'index'])
        ->middleware('permission:email.manage_settings')
        ->name('email.settings.index');
    Route::put('email/settings', [EmailSettingsController::class, 'update'])
        ->middleware('permission:email.manage_settings')
        ->name('email.settings.update');
    Route::post('email/settings/test', [EmailSettingsController::class, 'test'])
        ->middleware('permission:email.manage_settings')
        ->name('email.settings.test');

    // Email Accounts Management
    Route::middleware('permission:email.manage')->group(function () {
        Route::resource('email-accounts', EmailAccountController::class);
        Route::get('email-accounts/{emailAccount}/available-users', [EmailAccountController::class, 'availableUsers'])
            ->name('email-accounts.available-users');
        Route::get('email-accounts-stats', [EmailAccountController::class, 'stats'])
            ->name('email-accounts.stats');

        Route::post('email-accounts/{emailAccount}/assign', [EmailAssignmentController::class, 'assign'])
            ->name('email-accounts.assign');
        Route::delete('email-accounts/{emailAccount}/unassign/{user}', [EmailAssignmentController::class, 'unassign'])
            ->name('email-accounts.unassign');
        Route::patch('email-accounts/{emailAccount}/permissions/{user}', [EmailAssignmentController::class, 'updatePermissions'])
            ->name('email-accounts.permissions.update');
        Route::post('email-accounts/{emailAccount}/bulk-assign', [EmailAssignmentController::class, 'bulkAssign'])
            ->name('email-accounts.bulk-assign');
        Route::post('email-accounts/{emailAccount}/transfer-primary', [EmailAssignmentController::class, 'transferPrimary'])
            ->name('email-accounts.transfer-primary');
        Route::get('users/{user}/emails', [EmailAssignmentController::class, 'userEmails'])
            ->name('users.emails');
    });
});
