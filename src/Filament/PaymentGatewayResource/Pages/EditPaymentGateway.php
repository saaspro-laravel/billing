<?php

namespace Utyemma\SaasPro\Filament\Resources\Payments\PaymentGatewayResource\Pages;

use Utyemma\SaasPro\Filament\Resources\Payments\PaymentGatewayResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPaymentGateway extends EditRecord
{
    protected static string $resource = PaymentGatewayResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
