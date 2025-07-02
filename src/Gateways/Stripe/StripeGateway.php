<?php

namespace Utyemma\SaasPro\PaymentGateways\Stripe;

use Utyemma\SaasPro\Abstracts\BasePaymentGateway;
use Utyemma\SaasPro\Contracts\Payment\HandlesCheckout;
use Utyemma\SaasPro\Contracts\Payment\HandlesSubscription;
use Utyemma\SaasPro\Contracts\Payment\HandlesWebhook;
use Utyemma\SaasPro\Contracts\Payment\RedirectPayment;
use Utyemma\SaasPro\Enums\PaymentStatus;
use Utyemma\SaasPro\Enums\RequestStatus;
use Utyemma\SaasPro\Models\Transactions\Transaction;
use Utyemma\SaasPro\PaymentGateways\Stripe\Concerns\ManagePayment;
use Utyemma\SaasPro\PaymentGateways\Stripe\Concerns\ManageSubscriptions;
use Utyemma\SaasPro\PaymentGateways\Stripe\Concerns\ManageWebhooks;
use Utyemma\SaasPro\Support\HttpResponse;
use Exception;
use Stripe;

class StripeGateway extends BasePaymentGateway implements RedirectPayment, HandlesSubscription, HandlesCheckout, HandlesWebhook {
    use ManageSubscriptions, ManagePayment, ManageWebhooks;

    function client(): Stripe\StripeClient {
        return new Stripe\StripeClient(env('STRIPE_SECRET'));
    }

    function verify(Transaction $transaction): HttpResponse {
        try {
            $session = $this->client->checkout->sessions->retrieve($transaction->provider_id);

            if(!isset($session->payment_status)) throw new Exception($session->message ?? 'Invalid Payment verification Response from payment provider');

            $state = match ($session->payment_status) {
                'paid' => PaymentStatus::SUCCESS,
                'unpaid' => PaymentStatus::PENDING,
                default => PaymentStatus::CANCELLED
            };

            return $this->response(RequestStatus::OK, $session->toArray(), $state);
        } catch (\Throwable $th) {
            return $this->response(RequestStatus::ERROR, ['message' => $th->getMessage()]);
        }
    }

    

    
    
    

}