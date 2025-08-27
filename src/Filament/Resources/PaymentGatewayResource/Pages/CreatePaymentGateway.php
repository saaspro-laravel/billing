<?php

namespace SaasPro\Billing\Filament\Resources\PaymentGatewayResource\Pages;

use SaasPro\Billing\Filament\Resources\PaymentGatewayResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePaymentGateway extends CreateRecord
{
    protected static string $resource = PaymentGatewayResource::class;
}
