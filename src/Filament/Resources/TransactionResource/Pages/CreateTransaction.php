<?php

namespace SaasPro\Billing\Filament\Resources\TransactionResource\Pages;

use SaasPro\Billing\Filament\Resources\TransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTransaction extends CreateRecord
{
    protected static string $resource = TransactionResource::class;
}
