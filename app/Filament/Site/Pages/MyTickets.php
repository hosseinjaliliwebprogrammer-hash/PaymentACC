<?php

namespace App\Filament\Site\Pages;

use Filament\Pages\Page;
use App\Models\Ticket;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;

class MyTickets extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    protected static ?string $navigationLabel = 'My Tickets';
    protected static ?string $title = 'My Tickets';
    protected static ?string $slug = 'my-tickets';

    protected static string $view = 'filament.site.pages.my-tickets';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Ticket::query()
                    ->where('user_id', auth()->id()) // فقط تیکت‌های کاربر فعلی
                    ->latest()
            )
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->sortable(),

                TextColumn::make('subject')
                    ->label('Subject')
                    ->searchable(),

                TextColumn::make('department')
                    ->label('Department')
                    ->badge(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->colors([
                        'open' => 'success',
                        'in_progress' => 'warning',
                        'closed' => 'gray',
                    ]),

                TextColumn::make('priority')
                    ->label('Priority')
                    ->badge()
                    ->colors([
                        'low' => 'gray',
                        'normal' => 'info',
                        'high' => 'danger',
                    ]),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->actions([
                Action::make('view')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->color('warning')
                    ->url(fn ($record) => route('filament.site.pages.view-ticket', ['record' => $record]))
                    ->openUrlInNewTab(),
            ]);
    }
}
