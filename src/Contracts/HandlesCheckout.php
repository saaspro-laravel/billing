<?php

namespace SaasPro\Billing\Contracts\Payment;

use SaasPro\Billing\Models\Transactions\Transaction;
use SaasPro\Support\State;

interface HandlesCheckout {

    public function startCheckout(Transaction $transaction): State;

    function getCheckoutId(mixed $response): string;

}