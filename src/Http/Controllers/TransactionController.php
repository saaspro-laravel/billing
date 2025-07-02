<?php

namespace Utyemma\SaasPro\Http\Controllers\Billing;

use Illuminate\Http\Request;
use Utyemma\SaasPro\Models\Subscription;
use Utyemma\SaasPro\Models\Transactions\Transaction;
use Utyemma\SaasPro\Services\SubscriptionService;
use Utyemma\SaasPro\Services\TransactionService;

class TransactionController {

    function __construct(
        private TransactionService $transactionService,
        private SubscriptionService $subscriptionService
    ) {

    }

    function verify(Request $request) {
        if(!$transaction = Transaction::whereReference($request->route('transaction'))->first()) abort(404);

        [$status, $message, $data] = match($transaction->transactable_type) {
            Subscription::class => $this->subscriptionService->subscribe($transaction),
            default => $this->transactionService->verify($transaction)
        };

        if(!$status) return to_route('dashboard')->with(['error' => $message]);
        return to_route('dashboard')->with(['success' => $message]);
    }

}
