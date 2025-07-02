<?php

namespace Utyemma\SaasPro\Contracts\Payment;

interface RedirectPayment {

    function callbackUrl(array $params): string;

}