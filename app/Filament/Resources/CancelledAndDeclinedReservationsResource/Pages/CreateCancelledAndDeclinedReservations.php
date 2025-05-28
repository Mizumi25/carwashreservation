<?php

namespace App\Filament\Resources\CancelledAndDeclinedReservationsResource\Pages;

use App\Filament\Resources\CancelledAndDeclinedReservationsResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCancelledAndDeclinedReservations extends CreateRecord
{
    protected static string $resource = CancelledAndDeclinedReservationsResource::class;
}
