<?php

namespace SaasPro\Billing\Contracts\Payment;

use SaasPro\Subscriptions\Models\Subscription;

interface HandlesSubscriptionRenewal {

    public function renew(Subscription $subscription): array;

}