<?php

namespace Utyemma\SaasPro\PaymentGateways\Lemonsqueezy;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Utyemma\SaasPro\Abstracts\BasePaymentGateway;
use Utyemma\SaasPro\Contracts\Payment\HandlesSubscription;
use Utyemma\SaasPro\Contracts\Payment\HandlesWebhook;
use Utyemma\SaasPro\Contracts\Payment\RedirectPayment;
use Utyemma\SaasPro\Enums\RequestStatus;
use Utyemma\SaasPro\Models\Transactions\Transaction;
use Utyemma\SaasPro\PaymentGateways\Lemonsqueezy\Concerns\ManageSubscriptions;
use Utyemma\SaasPro\PaymentGateways\Lemonsqueezy\Concerns\ManagesWebhooks;
use Utyemma\SaasPro\Support\HttpResponse;

class LemonSqueezy extends BasePaymentGateway implements HandlesSubscription, RedirectPayment, HandlesWebhook {
    use ManageSubscriptions, ManagesWebhooks;

    public function client(): PendingRequest {
        return Http::baseUrl(env('LEMON_SQUEEZY_URL'))
            ->withHeaders([
                'Accept' => 'application/vnd.api+json',
                'Content-Type' => 'application/vnd.api+json',
                'Authorization' => "Bearer ".env('LEMON_SQUEEZY_KEY')
            ]);
    }

    public function verify(Transaction $transaction): HttpResponse {
        $response = $this->buildResponse($this->client()->get("/checkouts/{$transaction->provider_id}"));

        return $this->response(RequestStatus::OK);
    }

    public function buildResponse(Response $response): HttpResponse {
        $data = $response->json();
        $isOk = str((string) $response->status())->charAt(0) == 2;
        
        if(!$isOk) {
            $message = $data['errors'][0]['detail'] ?? 'There was a problem with your request';

            return $this->response(RequestStatus::ERROR, [
                'error' => $message
            ], $data);
        }

        return $this->response(RequestStatus::OK, $data);
    }

}

