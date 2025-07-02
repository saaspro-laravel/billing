<?php

namespace Utyemma\SaasPro\Contracts\Payment;

use Utyemma\SaasPro\Models\Transactions\Transaction;
use Utyemma\SaasPro\Support\HttpResponse;
use Illuminate\Http\Client\Response;

interface PaymentGateway {

    public function client(): mixed;

    public function verify(Transaction $transaction): HttpResponse;

    public function subscribe(Transaction $transaction): array;
    // public function onResponse(HttpResponse $httpResponse, Transaction $transaction): array;
    
    public function checkout(Transaction $transaction): array;

    public function buildResponse(Response $response): HttpResponse;

}