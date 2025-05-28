<?php

namespace App\Filament\Resources\RescheduleReservationResource\Pages;

use App\Filament\Resources\RescheduleReservationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRescheduleReservation extends EditRecord
{
    protected static string $resource = RescheduleReservationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
