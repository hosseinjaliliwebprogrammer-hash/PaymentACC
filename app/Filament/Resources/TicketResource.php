<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketResource\Pages;
use App\Models\Ticket;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    protected static ?string $navigationGroup = 'Support';
    protected static ?string $navigationLabel = 'Tickets';
    protected static ?string $pluralLabel = 'Tickets';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('User')
                    ->relationship('user', 'name')
                    ->required(),

                Forms\Components\TextInput::make('subject')
                    ->label('Subject')
                    ->required(),

                Forms\Components\Select::make('department')
                    ->label('Department')
                    ->options([
                        'sales' => 'Sales',
                        'technical' => 'Technical',
                    ])
                    ->required(),

                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'open' => 'Open',
                        'in_progress' => 'In Progress',
                        'closed' => 'Closed',
                    ])
                    ->default('open')
                    ->required(),

                Forms\Components\Select::make('priority')
                    ->label('Priority')
                    ->options([
                        'low' => 'Low',
                        'normal' => 'Normal',
                        'high' => 'High',
                    ])
                    ->default('normal')
                    ->required(),

                Forms\Components\RichEditor::make('message')
                    ->label('Message')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable()
                    ->label('#'),

                TextColumn::make('subject')
                    ->searchable()
                    ->label('Subject'),

                TextColumn::make('user.name')
                    ->label('User'),

                // Department badge with custom colors
                TextColumn::make('department')
                    ->badge()
                    ->label('Department')
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->colors([
                        'warning' => 'sales',     // مثلاً زرد برای Sales
                        'info'    => 'technical', // آبی برای Technical
                    ]),

                // Status badge with custom colors
                TextColumn::make('status')
                    ->badge()
                    ->label('Status')
                    ->formatStateUsing(function (string $state): string {
                        return match ($state) {
                            'in_progress' => 'In progress',
                            default       => ucfirst($state),
                        };
                    })
                    ->colors([
                        'success' => 'open',        // سبز برای Open
                        'warning' => 'in_progress', // زرد/نارنجی برای In progress
                        'danger'  => 'closed',      // قرمز برای Closed
                    ]),

                // Priority badge with custom colors
                TextColumn::make('priority')
                    ->badge()
                    ->label('Priority')
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->colors([
                        'success'   => 'low',    // سبز برای Low
                        'secondary' => 'normal', // خاکستری برای Normal
                        'danger'    => 'high',   // قرمز برای High
                    ]),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('department')
                    ->label('Department')
                    ->options([
                        'sales' => 'Sales',
                        'technical' => 'Technical',
                    ]),

                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'open' => 'Open',
                        'in_progress' => 'In Progress',
                        'closed' => 'Closed',
                    ]),
            ])
            ->defaultSort('created_at', 'desc') // جدیدترین تیکت‌ها اول
            ->actions([
                Tables\Actions\ViewAction::make(),   // دکمه View
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListTickets::route('/'),
            'create' => Pages\CreateTicket::route('/create'),
            'edit'   => Pages\EditTicket::route('/{record}/edit'),
            'view'   => Pages\ViewTicket::route('/{record}'),
        ];
    }
}
