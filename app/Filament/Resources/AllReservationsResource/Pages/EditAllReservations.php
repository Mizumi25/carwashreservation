<?php

namespace App\Filament\Resources\AllReservationsResource\Pages;

use App\Filament\Resources\AllReservationsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAllReservations extends EditRecord
{
    protected static string $resource = AllReservationsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
