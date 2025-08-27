<?php

namespace SaasPro\Billing\Contracts\Payment;

interface InlinePayment {

    function inline(): array;

}