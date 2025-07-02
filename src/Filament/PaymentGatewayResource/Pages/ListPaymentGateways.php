<?php

namespace Utyemma\SaasPro\Filament\Resources\Payments\PaymentGatewayResource\Pages;

use Utyemma\SaasPro\Filament\Resources\Payments\PaymentGatewayResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPaymentGateways extends ListRecords
{
    protected static string $resource = PaymentGatewayResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
