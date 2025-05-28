<?php

namespace App\Filament\Resources\CancelledAndDeclinedReservationsResource\Pages;

use App\Filament\Resources\CancelledAndDeclinedReservationsResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewCancelledAndDeclinedReservations extends ViewRecord
{
    protected static string $resource = CancelledAndDeclinedReservationsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
