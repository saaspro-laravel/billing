<?php

namespace SaasPro\Billing\Contracts\Payment;

use SaasPro\Billing\Support\HttpResponse;
use Illuminate\Http\Request;
use SaasPro\Support\State;

interface HandlesWebhook {
    
    function handleWebhook(array $payload): State;
    function verifyWebhook(Request $request): State;
    
    
}