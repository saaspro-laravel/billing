<?php

namespace Utyemma\SaasPro\PaymentGateways\Lemonsqueezy\Concerns;

use Utyemma\SaasPro\Enums\RequestStatus;
use Utyemma\SaasPro\Models\Plans\PlanPrice;
use Utyemma\SaasPro\Models\Subscription;
use Utyemma\SaasPro\Models\Transactions\Transaction;
use Utyemma\SaasPro\Support\HttpResponse;

trait ManageSubscriptions {

    function startSubscription (Transaction $transaction): HttpResponse {
        $subscription = $transaction->transactable;
        $user = $subscription->subscriber;

        $response = $this->buildResponse($this->client()->post('checkouts', [
            'data' => [
                "type" => "checkouts",
                "relationships" => [
                    "store" =>  [
                        "data" =>  [
                            "type" => "stores",
                            "id" => env('LEMON_SQUEEZY_STORE_ID')
                        ]
                    ],
                    "variant" => [
                        "data" => [
                            "type" => "variants",
                            "id" => $subscription->planPrice->provider_id
                        ]
                    ]
                ],
                "attributes" => [
                    "product_options" => [
                        "redirect_url" => route('billing')
                    ],
                    "checkout_data" => [
                        'email' => $user->email,
                        'custom' => [
                            'reference' => (string) $subscription->id
                        ]
                    ]
                ],
            ]
        ]));

        if($response->failed()) return $response;
        
        $context = $response->context();

        $transaction->provider_id = $context['data']['id'];
        $transaction->save();

        return $this->response(RequestStatus::OK, $context, $context['data']['attributes']['url']);        
    }

    function cancelSubscription(Subscription $subscription): HttpResponse {
        $response = $this->buildResponse($this->client()->delete("subscriptions/{$subscription->reference}"));

        if($response->failed()) return $response;

        $subscription->cancel();

        return $this->response(RequestStatus::OK, $response->context());
    }

    function upgradeSubscription(Subscription $subscription, PlanPrice $planPrice): HttpResponse {
        $response = $this->buildResponse($this->client()->patch("subscriptions/{$subscription->reference}", [
            'type' => 'subscriptions',
            'id' => $subscription->reference,
            'attributes' => [
                'attributes' => $planPrice->provider_id
            ]
        ]));

        if($response->failed()) return $response;
        
        $subscription->changePlan($planPrice->plan);
        
        return $this->response(RequestStatus::OK, $response->context());
    }
    
    function getSubscription(Subscription $subscription): HttpResponse {
        $response = $this->buildResponse($this->client()->get('subscriptions/{$subscription->provider_id}'));
        
        if($response->failed()) return $response;

        return $this->response(RequestStatus::OK, $response->context());
    }

    function getSubscriptionId($subscription): string {
        return $subscription['id'];
    }

    function getSubscriptionStatus(Subscription $subscription): HttpResponse {
        $response = $this->getSubscription($subscription);
        return $this->response(RequestStatus::OK);
    }

}