<?php

namespace App\Filament\Resources\PaypalPaymentSubmissionResource\Pages;

use App\Filament\Resources\PaypalPaymentSubmissionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPaypalPaymentSubmissions extends ListRecords
{
    protected static string $resource = PaypalPaymentSubmissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
