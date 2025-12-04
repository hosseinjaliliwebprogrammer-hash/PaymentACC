<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GatewayResource\Pages;
use App\Models\Gateway;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Columns\ImageColumn;

class GatewayResource extends Resource
{
    protected static ?string $model = Gateway::class;

    protected static ?string $navigationIcon  = 'heroicon-o-credit-card';
    protected static ?string $navigationGroup = 'Payments';
    protected static ?string $navigationLabel = 'Gateways';
    protected static ?string $pluralLabel     = 'Gateways';
    protected static ?string $slug            = 'gateways';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label('Name')
                ->required(),

            Forms\Components\TextInput::make('email')
                ->email()
                ->label('Email')
                ->required(),

            Forms\Components\TextInput::make('link')
                ->label('Link')
                ->url()
                ->nullable(),

            Forms\Components\Textarea::make('invoice_description')
                ->label('Invoice Description')
                ->helperText('Use #order for order ID and #product for product name.')
                ->rows(2)
                ->nullable(),

            Forms\Components\Select::make('email_template_type')
                ->label('Email Template Type')
                ->options([
                    'standard' => 'Standard',
                    'custom'   => 'Custom',
                    'Send' => 'Send',
                ])
                ->required(),

            Forms\Components\Select::make('email_template_id')
    ->label('Email Template')
    ->relationship('template', 'name') // نیازمند Gateway::template()
    ->searchable()
    ->preload()
    ->nullable()
    ->helperText('Choose the email template for this gateway.'),

            Forms\Components\TextInput::make('max_transactions')
                ->label('Max Transactions')
                ->numeric()
                ->minValue(0)
                ->default(0),

            Forms\Components\TextInput::make('limit_amount')
                ->numeric()
                ->label('Amount Limit ($)')
                ->minValue(0)
                ->required(),

            Forms\Components\TextInput::make('priority')
                ->label('Priority')
                ->numeric()
                ->default(1)
                ->minValue(1)
                ->helperText('Gateways with higher priority are chosen first.'),

            Forms\Components\FileUpload::make('logo')
                ->label('Logo')
                ->disk('public')
                ->directory('gateways/logos')
                ->visibility('public')
                ->image()
                ->preserveFilenames()
                ->maxSize(2048)
                ->nullable(),

            Forms\Components\Toggle::make('is_active')
                ->label('Active')
                ->default(true),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('logo')
                    ->label('Logo')
                    ->square()
                    ->size(40)
                    ->getStateUsing(fn ($record) => $record->logo ? asset('storage/' . $record->logo) : null),

                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),

                TextColumn::make('link')
                    ->label('Link')
                    ->url(fn ($record) => $record->link)
                    ->openUrlInNewTab()
                    ->wrap()
                    ->toggleable(),

                TextColumn::make('limit_amount')
                    ->label('Limit')
                    ->money('usd', true)
                    ->sortable(),

                TextColumn::make('used_amount')
                    ->label('Used')
                    ->money('usd', true)
                    ->sortable(),

                TextColumn::make('remaining')
                    ->label('Remaining')
                    ->state(fn($record) => $record->limit_amount == 0
                        ? '∞'
                        : number_format(max(0, $record->limit_amount - $record->used_amount), 2))
                    ->sortable(),

                TextColumn::make('email_template_type')
                    ->label('Template Type')
                    ->badge()
                    ->color('info'),

                TextColumn::make('max_transactions')
                    ->label('Max TX')
                    ->sortable(),

                TextColumn::make('priority')
                    ->label('Priority')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                Tables\Columns\TextColumn::make('template.name')
    ->label('Email Template')
    ->badge()
    ->color('info')
    ->placeholder('—')
    ->toggleable(),


                TextColumn::make('is_active')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn($state) => $state ? 'Active' : 'Inactive')
                    ->color(fn($state) => $state ? 'success' : 'danger'),

                ToggleColumn::make('is_active')
                    ->label('Toggle'),
            ])
            ->defaultSort('priority', 'desc')
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListGateways::route('/'),
            'create' => Pages\CreateGateway::route('/create'),
            'edit'   => Pages\EditGateway::route('/{record}/edit'),
        ];
    }
    public static function canViewAny(): bool
{
    return auth()->user()?->is_admin;
}

}
