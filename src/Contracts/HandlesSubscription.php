<?php

namespace SaasPro\Billing\Contracts\Payment;

use SaasPro\Billing\Models\Transactions\Transaction;
use SaasPro\Subscriptions\Models\PlanPrice;
use SaasPro\Subscriptions\Models\Subscription;
use SaasPro\Support\State;

interface HandlesSubscription {

    function startSubscription(Transaction $transaction): State;

    function cancelSubscription(Subscription $subscription): State;

    function getSubscriptionId(mixed $response): string;

    function upgradeSubscription(Subscription $subscription, PlanPrice $planPrice): State;

}