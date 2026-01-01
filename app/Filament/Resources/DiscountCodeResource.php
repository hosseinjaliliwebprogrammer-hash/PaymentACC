<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DiscountCodeResource\Pages;
use App\Filament\Resources\DiscountCodeResource\RelationManagers;
use App\Models\DiscountCode;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DiscountCodeResource extends Resource
{
    protected static ?string $model = DiscountCode::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationGroup = 'Payments';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('code')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('discount_percentage')
                    ->required()
                    ->numeric()
                    ->maxValue(100),
                Forms\Components\DateTimePicker::make('start_date'),
                Forms\Components\DateTimePicker::make('end_date'),
                Forms\Components\Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
                Forms\Components\Select::make('products')
                    ->label('Select Products')
                    ->multiple()
                    ->options(Product::all()->pluck('name', 'id')) // نمایش همه محصولات
                    ->relationship('products', 'name')
                    ->searchable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('discount_percentage')->sortable(),
                Tables\Columns\TextColumn::make('start_date')->dateTime(),
                Tables\Columns\TextColumn::make('end_date')->dateTime(),
                Tables\Columns\BooleanColumn::make('is_active')->sortable(),
                Tables\Columns\TextColumn::make('products.name')->label('Products'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDiscountCodes::route('/'),
            'create' => Pages\CreateDiscountCode::route('/create'),
            'edit' => Pages\EditDiscountCode::route('/{record}/edit'),
        ];
    }


}
