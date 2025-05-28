<?php

namespace App\Filament\Resources\CancelledAndDeclinedReservationsResource\Pages;

use App\Filament\Resources\CancelledAndDeclinedReservationsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCancelledAndDeclinedReservations extends EditRecord
{
    protected static string $resource = CancelledAndDeclinedReservationsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
