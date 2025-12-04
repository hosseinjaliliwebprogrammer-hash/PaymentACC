<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderWidgets(): array
    {
        return [];
    }

    public function infolist(\Filament\Infolists\Infolist $infolist): \Filament\Infolists\Infolist
    {
        return $infolist->schema([
            Section::make('Order Info')->schema([
                TextEntry::make('id')->label('Order ID'),
                TextEntry::make('name')->label('Customer'),
                TextEntry::make('email')->label('Email'),
                TextEntry::make('service')->label('Service'),
                TextEntry::make('amount')->label('Amount ($)'),
                TextEntry::make('status')->label('Status'),
                TextEntry::make('tracking_code')->label('Tracking Code'),
            ])->columns(2),

            Section::make('Payment Instructions')->schema([
                TextEntry::make('payment_instructions.provider')->label('Provider'),
                TextEntry::make('payment_instructions.display')->label('Account'),
                TextEntry::make('payment_instructions.email')->label('PayPal Email'),
                TextEntry::make('payment_instructions.url')
                    ->label('Pay URL')
                    ->url(fn ($record) => data_get($record, 'payment_instructions.url'))
                    ->openUrlInNewTab(),
                TextEntry::make('payment_instructions.email_subject')->label('Email Subject'),
                TextEntry::make('payment_instructions.email_body')
                    ->label('Email Body')
                    ->formatStateUsing(fn ($state) => nl2br(e($state)))
                    ->html()
                    ->columnSpanFull(),
            ])->columns(2),
        ]);
    }
}
