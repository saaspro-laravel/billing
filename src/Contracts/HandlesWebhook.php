<?php

namespace Utyemma\SaasPro\Contracts\Payment;

use Utyemma\SaasPro\Support\HttpResponse;
use Illuminate\Http\Request;

interface HandlesWebhook {
    
    function handleWebhook(array $payload): HttpResponse;
    function verifyWebhook(Request $request): HttpResponse;
    
    
}