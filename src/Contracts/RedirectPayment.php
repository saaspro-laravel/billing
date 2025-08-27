<?php

namespace SaasPro\Billing\Contracts\Payment;

interface RedirectPayment {

    function callbackUrl(array $params): string;

}