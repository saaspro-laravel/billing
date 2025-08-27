<?php

namespace SaasPro\Billing\Contracts\Payment;

use SaasPro\Billing\Models\Transactions\Transaction;
use SaasPro\Billing\Support\HttpResponse;
use Illuminate\Http\Client\Response;
use SaasPro\Support\State;

interface PaymentGateway {

    public function client(): mixed;

    public function verify(Transaction $transaction): State;

    public function subscribe(Transaction $transaction): array;
    // public function onResponse(HttpResponse $httpResponse, Transaction $transaction): array;
    
    public function checkout(Transaction $transaction): array;

    public function buildResponse(Response $response): State;

}