<?php

namespace Utyemma\SaasPro\Http\Controllers\Webhooks;

use Utyemma\SaasPro\Enums\PaymentGateways;
use Illuminate\Http\Request;

class WebhookController
{
    
    function payment(Request $request, $gateway) {
        if(!$gateway = PaymentGateways::tryFrom($gateway)) {
            abort(404, 'The requested payment gateway does not exist');
        }

        $payload = $request->all();
        [$status, $message] = $gateway->provider()->webhook($payload);
        if(!$status) return response($message, 400);
        return response('Webhook received successfully');
    }
    
}
