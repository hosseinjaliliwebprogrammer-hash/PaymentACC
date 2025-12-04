<?php

namespace App\Filament\Site\Pages;

use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\PaypalPaymentSubmission;
use Illuminate\Support\Facades\Auth;

class PaymentHistory extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationLabel = 'Payment History';
    protected static ?string $title = 'Payment History';

    // نمایش در منوی داشبورد
    protected static bool $shouldRegisterNavigation = true;

    protected static string $view = 'filament.site.pages.payment-history';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                PaypalPaymentSubmission::query()
                    ->where('user_id', Auth::id()) // فقط پرداخت‌های همین کاربر
                    ->latest()
            )
            ->columns([

                Tables\Columns\TextColumn::make('order_id')
                    ->label('Order')
                    ->sortable(),

                Tables\Columns\TextColumn::make('transaction_id')
                    ->label('Transaction ID')
                    ->searchable(),

                Tables\Columns\TextColumn::make('paypal_email')
                    ->label('PayPal Email'),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger'  => 'rejected',
                    ])
                    ->label('Status'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Submitted At')
                    ->dateTime(),

                Tables\Columns\TextColumn::make('screenshot_path')
                    ->label('Screenshot')
                    ->formatStateUsing(fn ($state) => $state ? 'View' : '-')
                    ->url(fn ($record) => $record->screenshot_path ? asset('storage/'.$record->screenshot_path) : null)
                    ->openUrlInNewTab(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
