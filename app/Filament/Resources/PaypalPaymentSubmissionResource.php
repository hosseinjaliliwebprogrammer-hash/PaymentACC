<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaypalPaymentSubmissionResource\Pages;
use App\Models\PaypalPaymentSubmission;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;

class PaypalPaymentSubmissionResource extends Resource
{
    protected static ?string $model = PaypalPaymentSubmission::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'PayPal Payments';
    protected static ?string $pluralModelLabel = 'PayPal Payments';
    protected static ?string $modelLabel = 'PayPal Payment';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('transaction_id')
                ->label('Transaction ID')
                ->disabled(),

            Forms\Components\TextInput::make('paypal_email')
                ->label('PayPal Email')
                ->disabled(),

            Forms\Components\TextInput::make('status')
                ->label('Status')
                ->disabled(),

            Forms\Components\FileUpload::make('screenshot_path')
                ->label('Screenshot')
                ->directory('paypal_screenshots')
                ->image()
                ->disabled(),

            Forms\Components\TextInput::make('user_id')
                ->label('User ID')
                ->disabled(),

            Forms\Components\TextInput::make('order_id')
                ->label('Order ID')
                ->disabled(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('order.id')
                    ->label('Order ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('transaction_id')
                    ->label('Transaction ID')
                    ->searchable(),

                Tables\Columns\TextColumn::make('paypal_email')
                    ->label('PayPal Email')
                    ->searchable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger'  => 'rejected',
                    ])
                    ->label('Status'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Submitted At')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('screenshot_path')
                    ->label('Screenshot')
                    ->formatStateUsing(fn ($state) => $state ? 'View' : '-')
                    ->url(fn ($record) => $record->screenshot_path ? asset('storage/' . $record->screenshot_path) : null)
                    ->openUrlInNewTab(),
            ])

            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
            ])

            ->actions([

                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(function ($record) {

                        // وضعیت PayPal
                        $record->update(['status' => 'approved']);

                        // آپدیت سفارش
                        $record->order->update(['status' => 'paid']);

                        // ارسال ایمیل تحویل
                        \Mail::to($record->user->email)->send(
                            new \App\Mail\OrderDeliveryMail($record->order)
                        );

                        Notification::make()
                            ->title('Payment Approved & Delivery Email Sent')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('reject')
                    ->label('Reject')
                    ->color('danger')
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(function ($record) {

                        $record->update(['status' => 'rejected']);

                        Notification::make()
                            ->title('Payment Rejected')
                            ->danger()
                            ->send();
                    }),

                Tables\Actions\EditAction::make(),
            ])

            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPaypalPaymentSubmissions::route('/'),
            'edit'  => Pages\EditPaypalPaymentSubmission::route('/{record}/edit'),
        ];
    }
}
