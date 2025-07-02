<?php

namespace Utyemma\SaasPro\PaymentGateways\Lemonsqueezy\Enums;

enum SubscriptionStatus:string {

    case EXPIRED = 'expired';
    case ON_TRIAL = 'on_trial';
    case ACTIVE = 'active';
    case PAUSED = 'paused';
    case CANCELLED = 'cancelled';

    

}