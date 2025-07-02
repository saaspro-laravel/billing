<?php

namespace Utyemma\SaasPro\Contracts\Payment;

use Utyemma\SaasPro\Models\Plans\PlanPrice;
use Utyemma\SaasPro\Models\Subscription;
use Utyemma\SaasPro\Models\Transactions\Transaction;
use Utyemma\SaasPro\Support\HttpResponse;

interface HandlesSubscription {

    function startSubscription(Transaction $transaction): HttpResponse;

    function cancelSubscription(Subscription $subscription): HttpResponse;

    function getSubscriptionId(mixed $response): string;

    function upgradeSubscription(Subscription $subscription, PlanPrice $planPrice): HttpResponse;

}