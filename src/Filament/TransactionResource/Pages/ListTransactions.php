<?php

namespace Utyemma\SaasPro\Filament\Resources\Billing\TransactionResource\Pages;

use Utyemma\SaasPro\Filament\Resources\Billing\TransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
