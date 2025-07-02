<?php

namespace Utyemma\SaasPro\PaymentGateways\Stripe\Concerns;

use Utyemma\SaasPro\Enums\RequestStatus;
use Utyemma\SaasPro\Enums\Subscriptions\SubscriptionActions;
use Utyemma\SaasPro\Enums\SubscriptionStatus;
use Utyemma\SaasPro\Models\Subscription;
use Utyemma\SaasPro\Support\HttpResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Subscription as StripeSubscription;
use Stripe\WebhookSignature;

trait ManageWebhooks {

    function verifyWebhook(Request $request): HttpResponse{        
        try {
            WebhookSignature::verifyHeader(
                $request->getContent(),
                $request->header('Stripe-Signature'),
                env('STRIPE_SECRET')
            );
        } catch (SignatureVerificationException $exception) {
            return $this->response(RequestStatus::ERROR, $exception->getTrace(), $exception->getMessage());
        }

        return $this->response(RequestStatus::OK);
    }
    
    function handleWebhook(array $payload): HttpResponse {
        $method = 'handle'.str(str_replace('.', '_', $payload['type']))->studly();

        if (method_exists($this, $method)) {
            $response = $this->{$method}($payload);
            return $response;
        }

        return $this->response(RequestStatus::OK, []);
    }

    function handleCustomerSubscriptionUpdated($payload) {
        $subscription = Subscription::byReference($payload['data']['object']['id'])->firstOrFail();
        $data = $payload['data']['object'];

        if(!isset($data['status'])) return $this->response(RequestStatus::ERROR, $payload, 'Invalid payload');
            
        if($data['status'] === StripeSubscription::STATUS_INCOMPLETE_EXPIRED) {
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

        if (isset($data['cancel_at_period_end']) && is_string($data['cancel_at_period_end'])) {
            $nextExpirationDate = Carbon::createFromTimestamp($data['current_period_end']);
        } elseif (isset($data['cancel_at']) || isset($data['canceled_at'])) {
            $nextExpirationDate = Carbon::createFromTimestamp($data['cancel_at'] ?? $data['canceled_at']);
        }
        
        return $this->response(RequestStatus::OK, [
            'payload' => $payload,
            'subscription' => $subscription,
            'status' => SubscriptionStatus::ACTIVE,
            'expires_at' => $nextExpirationDate
        ]);
    }

    

}