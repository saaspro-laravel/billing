<?php

namespace Utyemma\SaasPro\PaymentGateways\Stripe\Concerns;

use Utyemma\SaasPro\Enums\RequestStatus;
use Utyemma\SaasPro\Models\Plans\PlanPrice;
use Utyemma\SaasPro\Models\Subscription;
use Utyemma\SaasPro\Models\Transactions\Transaction;
use Utyemma\SaasPro\Support\HttpResponse;
trait ManageSubscriptions {

    function startSubscription(Transaction $transaction): HttpResponse {
        $subscription = $transaction->transactable;
        try {
            $checkout = $this->client->checkout->sessions->create([
                'success_url' => $this->callbackUrl([
                    'transaction' => $transaction->id
                ]),
                'line_items' => [[
                    'price' => $subscription->planPrice->provider_id,
                    'quantity' => 1,
                ]],
                'mode' => 'subscription',
                'client_reference_id' => $transaction->reference,
                'customer_email' => $transaction->payload['email'],
                'currency' => strtolower($transaction->currency_code)
            ]);

            $transaction->provider_id = $checkout->id;
            $transaction->save();

            return $this->response(RequestStatus::OK, $checkout, $checkout->url);
        } catch (\Throwable $th) {
            return $this->response(RequestStatus::ERROR, ['error' => $th->getMessage()]);
        }
    }

    function cancelSubscription(Subscription $subscription): HttpResponse {
        try {
            $response = $this->client->subscriptions->update($subscription->reference, [
                'cancel_at_period_end' => true
            ]);

            $subscription->auto_renew = false;
            $subscription->save();

            return $this->response(RequestStatus::OK, $response);
        } catch (\Throwable $th) {
            return $this->response(RequestStatus::ERROR, [
                'error' => $th->getMessage()
            ], $th->getMessage());
        }
    }

    function getSubscription(Subscription $subscription) {
        try {
            $response = $this->client->retrieve($subscription->provider);
            return $this->response(RequestStatus::OK, $response);
        } catch (\Throwable $th) {
            return $this->response(RequestStatus::ERROR, [
                'error' => $th->getMessage()
            ], $th->getMessage());
        }
    }

    function getSubscriptionStatus(Subscription $subscription): HttpResponse {
        $response = $this->getSubscription($subscription);
        return $this->response(RequestStatus::OK);
    }

    function getSubscriptionId($response): string {
        return $response['subscription'];
    }

    function upgradeSubscription(Subscription $subscription, PlanPrice $planPrice): HttpResponse {
        $stripeSubscription = $this->getSubscription($subscription);
        
        if(!$stripeSubscription->success()) return $stripeSubscription;
        
        try {
            $subscriptionInfo = $stripeSubscription->context();

            $response = $this->client->update($subscription->reference, [
                'items' => [
                  [
                    'id' => $subscriptionInfo->items->data[0]->id,
                    'price' => $planPrice->provider_id,
                  ],
                ],
                'proration_date' => now()
              ]);

            $subscription->changePlan($planPrice->plan);

            return $this->response(RequestStatus::OK, $response);
        } catch (\Throwable $th) {
            return $this->response(RequestStatus::ERROR, [], $th->getMessage());
        }
    }

    function renew(Subscription $subscription) {
        $stripeSubscription = $this->getSubscription($subscription);
        if(!$stripeSubscription->success()) return $stripeSubscription;

        try {
            $subscriptionInfo = $stripeSubscription->context();
            $response = $this->client->invoice->pay($subscriptionInfo['next_invoice_id']);

            return $this->response(RequestStatus::OK, $response);
        } catch (\Throwable $th) {
            return $this->response(RequestStatus::ERROR, [], $th->getMessage());
        }
    }

}