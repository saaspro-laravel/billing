<?php

namespace Utyemma\SaasPro\Http\Middleware;

use Utyemma\SaasPro\Enums\PaymentGateways;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyWebhookRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, PaymentGateways $gateway): Response {
        $response = $gateway->provider()->verifyWebhook($request);
        if($response->failed()) abort(401, $response->message());

        return $next($request);
    }
}
