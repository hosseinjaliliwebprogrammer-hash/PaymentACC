<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmailTemplateResource\Pages;
use App\Models\EmailTemplate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EmailTemplateResource extends Resource
{
    protected static ?string $model = EmailTemplate::class;

    protected static ?string $navigationIcon  = 'heroicon-o-envelope';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $navigationLabel = 'Email Templates';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label('Template Name')
                ->required(),

            Forms\Components\TextInput::make('slug')
                ->label('Slug')
                ->unique(ignoreRecord: true)
                ->helperText('Used in code, e.g. "standard", "custom".')
                ->required(),

            Forms\Components\TextInput::make('subject')
                ->label('Email Subject')
                ->placeholder('Example: Your order has been created')
                ->required(),

            Forms\Components\RichEditor::make('body')
                ->label('Email Body')
                ->toolbarButtons([
                    'bold',
                    'italic',
                    'underline',
                    'strike',
                    'link',
                    'h2',
                    'h3',
                    'orderedList',
                    'bulletList',
                    'blockquote',
                    'codeBlock',
                    'horizontalRule',
                    'table',
                    'undo',
                    'redo',
                    'image',
                ])
                ->fileAttachmentsDisk('public')
                ->fileAttachmentsDirectory('uploads/email-images')
                ->hint('You can use variables: {{name}}, {{service}}, {{amount}}, {{tracking_code}}')
                ->columnSpanFull()
                ->required(),

            Forms\Components\Toggle::make('is_active')
                ->label('Active')
                ->default(true),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->badge()
                    ->color('info')
                    ->searchable(),

                Tables\Columns\TextColumn::make('subject')
                    ->label('Subject')
                    ->limit(60)
                    ->tooltip(fn($record) => $record->subject),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active')
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
            ])
            ->defaultSort('id', 'desc')
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListEmailTemplates::route('/'),
            'create' => Pages\CreateEmailTemplate::route('/create'),
            'edit'   => Pages\EditEmailTemplate::route('/{record}/edit'),
        ];
    }
    public static function canViewAny(): bool
{
    return auth()->user()?->is_admin;
}

}
