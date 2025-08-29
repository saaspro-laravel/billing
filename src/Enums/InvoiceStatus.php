<?php

namespace SaasPro\Billing\Enums;

enum InvoiceStatus:string {

    case UNPAID = 'unpaid';
    case PARTIALLY_PAID = 'partially_paid';
    case PAID = 'paid';

}