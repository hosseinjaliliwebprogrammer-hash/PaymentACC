<?php

namespace App\Filament\Resources\DiscountCodeResource\Pages;

use App\Filament\Resources\DiscountCodeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateDiscountCode extends CreateRecord
{
    protected static string $resource = DiscountCodeResource::class;


    protected function handleRecordCreation(array $data): Model
    {
        $record = new ($this->getModel())($data);

        // ارتباط محصولات با کد تخفیف
        if (isset($data['products']) && !empty($data['products'])) {
            $record->save();
            $record->products()->sync($data['products']); // اتصال محصولات به کد تخفیف
        } else {
            $record->save();
        }

        return $record;
    }
}
