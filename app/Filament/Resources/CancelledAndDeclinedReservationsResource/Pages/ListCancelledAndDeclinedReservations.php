<?php

namespace App\Filament\Resources\CancelledAndDeclinedReservationsResource\Pages;

use App\Filament\Resources\CancelledAndDeclinedReservationsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Models\Reservation;

class ListCancelledAndDeclinedReservations extends ListRecords
{
    protected static string $resource = CancelledAndDeclinedReservationsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            
        ];
    }
    
    public function getTabs(): array 
    {
      return [
        'declined' => ListRecords\Tab::make()->query(fn ($query) => $query->where('status', 'decline'))
        ->badge($this->getCountByStatus('decline')),
        'cancelled' => ListRecords\Tab::make()->query(fn ($query) => $query->where('status', 'cancelled'))
        ->badge($this->getCountByStatus('cancelled')),
        ];
    }
    protected function getTotalCount(): int
    {
        return Reservation::withTrashed()->count();
    }

    protected function getCountByStatus(string $status): int
    {
        return Reservation::where('status', $status)->withTrashed()->count();
    }
}
