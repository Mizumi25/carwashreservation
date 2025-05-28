<?php

namespace App\Filament\Resources\PendingReservationResource\Pages;

use App\Filament\Resources\PendingReservationResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManagePendingReservations extends ManageRecords
{
    protected static string $resource = PendingReservationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
