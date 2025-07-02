<?php

namespace Utyemma\SaasPro\Contracts\Payment;

use Utyemma\SaasPro\Models\Transactions\Transaction;
use Utyemma\SaasPro\Support\HttpResponse;

interface HandlesCheckout {

    public function startCheckout(Transaction $transaction): HttpResponse;

    function getCheckoutId(mixed $response): string;

}