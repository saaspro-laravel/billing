<?php

namespace Utyemma\SaasPro\PaymentGateways\Lemonsqueezy\Concerns;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Utyemma\SaasPro\Enums\RequestStatus;
use Utyemma\SaasPro\Enums\SubscriptionStatus;
use Utyemma\SaasPro\Exceptions\InvalidWebhookPayload;
use Utyemma\SaasPro\Exceptions\NotFoundException;
use Utyemma\SaasPro\Support\HttpResponse;

trait ManagesWebhooks {

    function verifyWebhook(Request $request): HttpResponse {
        $secret = env('LEMON_SQUEEZY_SIGNING_KEY');
        $payload = file_get_contents('php://input');

        $hash = hash_hmac('sha256', $payload, $secret);
        $signature = $_SERVER['HTTP_X_SIGNATURE'] ?? '';
        
        if(hash_equals($hash, $signature)) {
            return $this->response(RequestStatus::OK);
        }

        return $this->response(RequestStatus::ERROR);
    }

    function getSubscriptionByPayload(array $payload) {
        if(!$subscription = $this->findSubscription($payload['data']['id'])) throw new NotFoundException();
        return $subscription;
    }
    
    function handleWebhook(array $payload): HttpResponse { 
        
        if (! isset($payload['meta']['event_name'])) {
            return $this->response(RequestStatus::ERROR, [], 'Webhook received but no event name was found.');
        }
        
        $method = "handle".str($payload['meta']['event_name'])->studly();

        if(method_exists($this, $method)) {
            return $this->{$method}($payload);
        }

        return $this->response(RequestStatus::OK);
    }

    function handleSubscriptionCreated($payload){
        if(!$subscriptionId = $payload['meta']['custom_data']['reference'] ?? null) {
            InvalidWebhookPayload::throw('Subscription ID not found in webhook payload');
        }
        
        if(!$subscription = $this->findSubscription($subscriptionId, 'id')) {
            InvalidWebhookPayload::throw('Subscription ID not valid');
        }

        $subscription->activate();

        $subscription->reference = $payload['data']['id'];
        $subscription->save();

        return $this->response(RequestStatus::OK, [
            'payload' => $payload,
            'subscription' => $subscription,
            'status' => SubscriptionStatus::ACTIVE,
            'expires_at' => $subscription->expires_at
        ]);
    }

    function handleSubscriptionExpired($payload){
        $subscription = $this->getSubscriptionByPayload($payload);

        if($subscription->grace_ends_at && $subscription->grace_ends_at->isFuture()) {
            return $this->response(RequestStatus::OK, [
                'payload' => $payload,
                'subscription' => $subscription,
                'status' => SubscriptionStatus::GRACE
            ]);
        }

        return $this->response(RequestStatus::OK, [
            'payload' => $payload,
            'susbcription' => $subscription,
            'status' => SubscriptionStatus::EXPIRED
        ]);
    }

    function handleSubscriptionUpdated($payload) {
        $subscription = $this->getSubscriptionByPayload($payload);

        $status = $payload['attributes']['status'];

        if($status == 'cancelled') {
            if($subscription->grace_ends_at && $subscription->grace_ends_at->isFuture()) {
                return $this->response(RequestStatus::OK, [
                    'payload' => $payload,
                    'subscription' => $subscription,
                    'status' => SubscriptionStatus::GRACE
                ]);
            }

            return $this->response(RequestStatus::OK, [
                'payload' => $payload,
                'susbcription' => $subscription,
                'status' => SubscriptionStatus::EXPIRED
            ]);
        }

        $nextExpirationDate = $subscription->next_expiry_date;

        if(isset($payload['attributes']['ends_at']) && !!$payload['attributes']['ends_at']) {
            $nextExpirationDate = Carbon::createFromTimestamp($payload['attributes']['ends_at']);
        }

        return $this->response(RequestStatus::OK, [
            'payload' => $payload,
            'subscription' => $subscription,
            'status' => SubscriptionStatus::ACTIVE,
            'expires_at' => $nextExpirationDate
        ]);
    }

}