<?php

namespace Utyemma\SaasPro\Contracts\Payment;

use Utyemma\SaasPro\Models\Subscription;

interface HandlesSubscriptionRenewal {

    public function renew(Subscription $subscription): array;

}