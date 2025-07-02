<?php

namespace Utyemma\SaasPro\PaymentGateways\Stripe\Concerns;

use Utyemma\SaasPro\Enums\RequestStatus;
use Utyemma\SaasPro\Models\Transactions\Transaction;
use Utyemma\SaasPro\Support\HttpResponse;

trait ManagePayment {

    function startCheckout(Transaction $transaction): HttpResponse {
        $callbackUrl = $this->callbackUrl(['reference' => $transaction->reference]);
        
        try{
            $checkout = $this->stripeClient->checkout->sessions->create([
                'success_url' => $callbackUrl,
                'cancel_url' => $callbackUrl,
                'line_items' => [
                    [
                        'price' => $transaction->payload['provider_id'],
                        'quantity' => $transaction->payload['quantity'],
                    ]
                ],
                'mode' => 'payment',
                'client_reference_id' => $transaction->reference,
                'customer_email' => $transaction->payload['email'],
                'currency' => strtolower($transaction->currency_code)
            ]);

            return $this->response(RequestStatus::OK, [
                'url' => $checkout->url
            ]);
        }catch(\Exception $e){
            return $this->response(RequestStatus::ERROR, ['message' => $e->getMessage()]);
        }
    }

    function getCheckoutId($response): string {
        return $response['id'];
    }

}