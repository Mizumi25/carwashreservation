<?php

namespace App\Filament\Resources\RescheduleReservationResource\Pages;

use App\Filament\Resources\RescheduleReservationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRescheduleReservations extends ListRecords
{
    protected static string $resource = RescheduleReservationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
