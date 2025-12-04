<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use App\Models\Gateway;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use App\Filament\Site\Pages\OrderSummary;
use Illuminate\Database\Eloquent\Builder;

use App\Mail\OrderDeliveryMail;
use Illuminate\Support\Facades\Mail;
use Filament\Notifications\Notification;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon  = 'heroicon-o-shopping-cart';
    protected static ?string $navigationGroup = 'Payments';
    protected static ?string $navigationLabel = 'Orders';
    protected static ?string $pluralLabel     = 'Orders';
    protected static ?string $slug            = 'orders';

    /** Form (Create / Edit) */
    public static function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\TextInput::make('name')
                ->label('Customer Name')
                ->required(),

            Forms\Components\TextInput::make('email')
                ->email()
                ->label('Email')
                ->required(),

            Forms\Components\TextInput::make('service')
                ->label('Service Name')
                ->required(),

            Forms\Components\TextInput::make('amount')
                ->numeric()
                ->label('Amount ($)')
                ->required()
                ->disabled(fn ($record) => filled($record)),

            Forms\Components\Select::make('gateway_id')
                ->label('Gateway')
                ->options(fn () => Gateway::pluck('email', 'id'))
                ->searchable()
                ->helperText('If left empty, a suitable gateway will be auto-assigned.')
                ->nullable()
                ->disabled(fn ($record) => filled($record)),

            Forms\Components\Textarea::make('description')
                ->label('Description')
                ->rows(2)
                ->nullable(),

            Forms\Components\Select::make('status')
                ->label('Status')
                ->options([
                    'pending'   => 'Pending',
                    'success'   => 'Paid',
                    'failed'    => 'Failed',
                    'completed' => 'Completed',
                ])
                ->default('pending')
                ->required(),

            Forms\Components\TextInput::make('tracking_code')
                ->label('Tracking Code')
                ->disabled()
                ->dehydrated(false),

            Forms\Components\Section::make('Delivery Information')
                ->description('Fill these when delivering the account to the customer.')
                ->schema([
                    Forms\Components\TextInput::make('delivery_server')
                        ->label('Server')
                        ->maxLength(255),

                    Forms\Components\TextInput::make('delivery_username')
                        ->label('Username')
                        ->maxLength(255),

                    Forms\Components\TextInput::make('delivery_password')
                        ->label('Password')
                        ->password()
                        ->maxLength(255),

                    Forms\Components\Textarea::make('delivery_notes')
                        ->label('Notes for customer')
                        ->rows(3),
                ])
                ->collapsible()
                ->collapsed(),

        ])->columns(2);
    }

    /** Table */
    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->columns([
                TextColumn::make('id')->label('ID')->sortable(),

                TextColumn::make('name')
                    ->label('Customer')
                    ->searchable()
                    ->visible(fn () => filament()->getCurrentPanel()?->getId() === 'admin'),

                TextColumn::make('email')->label('Email')->searchable(),
                TextColumn::make('service')->label('Service')->sortable(),
                TextColumn::make('amount')->label('Amount ($)')->money('usd', true)->sortable(),

                TextColumn::make('gateway.email')
                    ->label('Gateway')
                    ->sortable()
                    ->visible(fn () => filament()->getCurrentPanel()?->getId() === 'admin'),

                // ✅ ستون برای تشخیص منبع پرداخت (PayPal / NOWPayments / Paygate)
                BadgeColumn::make('payment_instructions.provider')
                    ->label('Provider')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'paypal'  => 'PayPal',
                        'nowpay'  => 'NOWPayments',
                        'paygate' => 'Paygate',
                        default   => $state ? ucfirst((string) $state) : '',
                    })
                    ->colors([
                        'primary' => 'paypal',
                        'warning' => 'nowpay',
                        'success' => 'paygate',
                    ])
                    ->sortable(),

                TextColumn::make('payment_instructions.email_body')
                    ->label('Email Body (preview)')
                    ->limit(40)
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),

                // ❌ ستون Status از جدول حذف شد (طبق درخواست تو)

                // ✅ ستون جدید: نتیجه Callback فقط برای NOWPayments و Paygate
                BadgeColumn::make('callback_result')
    ->label('Payment Callback')
    ->getStateUsing(function ($record) {
        $provider = data_get($record, 'payment_instructions.provider');
        $status   = (string) $record->status;

        // 🔵 از اینجا: رفتار مشترک برای همه گیت‌وی‌ها (PayPal + NOWPayments + Paygate)
        // PayPal هم دستی بر اساس status بررسی می‌شود
        if (in_array($status, ['success', 'completed'], true)) {
            return 'Payment Successful';
        }

        if ($status === 'failed') {
            return 'Payment Failed';
        }

        if ($status === 'pending') {
            return 'Pending';
        }

        return null;
    })
    ->colors([
        'success' => fn (?string $state): bool => $state === 'Payment Successful',
        'danger'  => fn (?string $state): bool => $state === 'Payment Failed',
        'warning' => fn (?string $state): bool => $state === 'Pending',
    ])
    ->sortable(),


                TextColumn::make('tracking_code')
                    ->label('Tracking Code')
                    ->copyable()
                    ->copyMessage('Copied!')
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable(),
            ])

            ->modifyQueryUsing(function ($query) {
                $panelId = filament()->getCurrentPanel()?->getId();
                if ($panelId === 'site') {
                    return auth()->guest()
                        ? $query->whereRaw('0 = 1')
                        : $query->where('user_id', auth()->id());
                }
            })

            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending'   => 'Pending',
                        'success'   => 'Paid',
                        'failed'    => 'Failed',
                        'completed' => 'Completed',
                    ]),
            ])

            ->defaultSort('created_at', 'desc')

            ->actions([

                Tables\Actions\Action::make('summary')
                    ->label('View Summary')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->url(fn ($record) =>
                        OrderSummary::getUrl(
                            ['tracking_code' => $record->tracking_code],
                            panel: 'site'
                        )
                    )
                    ->openUrlInNewTab()
                    ->visible(fn ($record) =>
                        auth()->user()?->is_admin ||
                        auth()->id() === $record->user_id
                    ),

                Tables\Actions\Action::make('view_email_body')
                    ->label('View Email')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('Email Body')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close')
                    ->visible(fn ($record) =>
                        filled(data_get($record, 'payment_instructions.email_body'))
                    )
                    ->modalContent(fn ($record) =>
                        view('components.view-email-body', [
                            'body' => (string) data_get($record, 'payment_instructions.email_body')
                        ])
                    ),

                Tables\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()?->is_admin),

                /** ---------------------------------------
                 * Send Delivery Email (Final Corrected Version)
                 * -------------------------------------- */
                Tables\Actions\Action::make('sendDeliveryEmail')
                    ->label('Send Delivery Email')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Send Delivery Email')
                    ->modalSubheading('Are you sure you want to send the delivery email for this order?')
                    ->modalButton('Confirm')
                    ->visible(fn ($record) =>
                        auth()->user()?->is_admin &&
                        $record->status === 'pending'
                    )
                    ->action(function ($record) {

                        if (! $record->email) {
                            Notification::make()
                                ->title('Order email not found')
                                ->danger()
                                ->body('This order does not contain a valid customer email.')
                                ->send();
                            return;
                        }

                        if (! $record->delivery_username ||
                            ! $record->delivery_password ||
                            ! $record->delivery_server) {

                            Notification::make()
                                ->title('Missing delivery information')
                                ->danger()
                                ->body('Please fill in server, username, and password before sending the delivery email.')
                                ->send();
                            return;
                        }

                        // 🚀 SEND TO ORDER EMAIL (fixed)
                        Mail::to($record->email)
                            ->send(new OrderDeliveryMail($record));

                        $record->status = 'completed';
                        $record->save();

                        Notification::make()
                            ->title('Delivery email sent successfully')
                            ->success()
                            ->body('The delivery email has been sent and the order status has been updated to completed.')
                            ->send();
                    }),
            ])

            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->visible(fn () => auth()->user()?->is_admin),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit'   => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        $panelId = filament()->getCurrentPanel()?->getId();

        if ($panelId === 'site') {
            return auth()->guest()
                ? $query->whereRaw('0 = 1')
                : $query->where('user_id', auth()->id());
        }

        return $query;
    }

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $data['tracking_code'] = $data['tracking_code'] ?? strtoupper(str()->random(10));
        $data['status']        = $data['status'] ?? 'pending';
        $data['user_id']       = auth()->id();

        if (empty($data['gateway_id'])) {
            if (method_exists(Gateway::class, 'pickForAmount')) {
                $gateway = Gateway::pickForAmount((float) $data['amount']);
                if (! $gateway) {
                    throw new \Exception('No active gateway with enough limit.');
                }
                $data['gateway_id'] = $gateway->id;
            }
        }

        return $data;
    }

    public static function afterCreate(Order $record): void
    {
        if ($record->gateway && method_exists($record->gateway, 'addUsage')) {
            $record->gateway->addUsage((float) $record->amount);
        }
    }

    public static function canViewAny(): bool
    {
        return true;
    }
}
