<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Gateway;
use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentOrders extends BaseWidget
{
    protected static ?string $heading = 'Recent Orders';
    protected int|string|array $columnSpan = 'full';

    protected function getTableQuery(): Builder
    {
        return Order::query()
            ->latest()
            ->when(! (auth()->user()?->is_admin ?? false), function (Builder $q) {
                $q->where('user_id', auth()->id());
            });
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('tracking_code')
                ->label('Tracking')
                ->copyable()
                ->searchable()
                ->toggleable(),

            Tables\Columns\TextColumn::make('name')
                ->label('Customer')
                ->searchable()
                ->limit(24),

            Tables\Columns\TextColumn::make('email')
                ->label('Email')
                ->limit(28)
                ->toggleable(isToggledHiddenByDefault: true),

            Tables\Columns\TextColumn::make('service')
                ->label('Service')
                ->limit(28),

            Tables\Columns\TextColumn::make('amount')
                ->label('Amount')
                ->money('usd', true)
                ->sortable(),

            Tables\Columns\TextColumn::make('gateway.name')
                ->label('Gateway')
                ->badge()
                ->placeholder('—')
                ->toggleable(),

            Tables\Columns\TextColumn::make('status')
                ->label('Status')
                ->badge()
                ->colors([
                    'warning' => fn ($state) => in_array($state, ['pending', 'waiting']),
                    'success' => fn ($state) => in_array($state, ['success', 'confirmed']),
                    'danger'  => fn ($state) => in_array($state, ['failed', 'canceled']),
                ])
                ->sortable(),

            Tables\Columns\TextColumn::make('created_at')
                ->label('Created')
                ->dateTime('Y-m-d H:i')
                ->since()
                ->sortable(),
        ];
    }

    protected function getTableFilters(): array
    {
        return [
            Tables\Filters\SelectFilter::make('status')
                ->options([
                    'pending'  => 'Pending',
                    'success'  => 'Success',
                    'failed'   => 'Failed',
                    'canceled' => 'Canceled',
                ]),

            // استفاده از options به‌جای relationship برای جلوگیری از null label
            Tables\Filters\SelectFilter::make('gateway_id')
                ->label('Gateway')
                ->options(
                    Gateway::query()
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->filter() // حذف null‌ها
                        ->toArray()
                ),

            Tables\Filters\Filter::make('today')
                ->label('Today')
                ->query(fn (Builder $q) => $q->whereDate('created_at', now()->toDateString())),
        ];
    }

    protected function isTablePaginationEnabled(): bool
    {
        return true;
    }

    protected function getDefaultTableRecordsPerPage(): int
    {
        return 10;
    }

    protected function getTableActions(): array
    {
        $isAdmin = auth()->user()?->is_admin ?? false;

        return [
            Tables\Actions\ViewAction::make()
                ->visible($isAdmin), // اگر صفحه View نداری می‌تونی حذفش کنی

            Tables\Actions\Action::make('markSuccess')
                ->label('Mark as Success')
                ->icon('heroicon-o-check-circle')
                ->requiresConfirmation()
                ->visible(fn (Order $record) => $isAdmin && $record->status !== 'success')
                ->action(function (Order $record) {
                    $record->update(['status' => 'success']);
                }),
        ];
    }
}
