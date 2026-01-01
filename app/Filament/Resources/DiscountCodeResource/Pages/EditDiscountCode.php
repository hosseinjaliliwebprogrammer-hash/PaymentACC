<?php

namespace App\Filament\Resources\DiscountCodeResource\Pages;

use App\Filament\Resources\DiscountCodeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditDiscountCode extends EditRecord
{
    protected static string $resource = DiscountCodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // بروزرسانی کد تخفیف
        $record->update($data);

        // بروزرسانی ارتباط میان کد تخفیف و محصولات
        if (isset($data['products']) && !empty($data['products'])) {
            $record->products()->sync($data['products']); // ارتباط محصولات به‌روزرسانی می‌شود
        }

        return $record;
    }


}
