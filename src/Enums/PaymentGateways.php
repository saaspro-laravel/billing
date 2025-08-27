<?php

namespace SaasPro\Billing\Enums;

use SaasPro\Billing\Contracts\Payment\RedirectPayment;
use SaasPro\Billing\Models\PaymentGateway;
use SaasPro\Billing\PaymentGateways\Lemonsqueezy\LemonSqueezy;
use SaasPro\Billing\PaymentGateways\Stripe\StripeGateway;

enum PaymentGateways:string {

    case PAYSTACK = 'paystack';
    case STRIPE = 'stripe';
    case LEMON_SQUEEZY = 'lemon_squeezy';

    function status(){
        return $this->model()->status;
    }

    function model() : PaymentGateway {
        return PaymentGateway::whereShortcode($this)->first();
    }

    function default(){
        return [
            'name' => $this->label(),
            'shortcode' => $this,
            'config' => config("payment.{$this->value}")
        ];
    }

    function provider(){
        return match ($this) {
            // self::STRIPE => StripeGateway::instance(),
            // self::LEMON_SQUEEZY => LemonSqueezy::instance()
        };
    }

    function label(){
        return match ($this) {
            self::STRIPE => "Stripe",
            self::PAYSTACK => "Paystack",
            self::LEMON_SQUEEZY => 'Lemon Squeezy'
        };
    }

    static function options(){
        return collect([
            self::STRIPE->value => self::STRIPE->label(),
            self::PAYSTACK->value => self::PAYSTACK->label(),
            self::LEMON_SQUEEZY->value => self::LEMON_SQUEEZY->label(),
        ]);
    }

    function allowsRedirect(){
        return $this->provider() instanceOf RedirectPayment;
    }

}