<?php

use Illuminate\Support\Facades\Route;
use Utyemma\SaasPro\Http\Controllers\Webhooks\WebhookController;
use Utyemma\SaasPro\Http\Middleware\VerifyWebhookRequest;

Route::prefix('webhooks')->name('webhooks.')->group(function() {
    Route::prefix('payments')->group(function(){
        Route::post('{gateway}', [WebhookController::class, 'payment'])
            ->name('webhook.payment');
    })->middleware(VerifyWebhookRequest::class);
});
