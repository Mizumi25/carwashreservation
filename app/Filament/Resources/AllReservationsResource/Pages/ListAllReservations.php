<?php

namespace App\Filament\Resources\AllReservationsResource\Pages;

use App\Filament\Resources\AllReservationsResource;
use Filament\Actions;
use Illuminate\Support\HtmlString;
use Filament\Resources\Pages\ListRecords;
use App\Models\Reservation;

class ListAllReservations extends ListRecords
{
    protected static string $resource = AllReservationsResource::class;

    public $defaultAction = 'newReservationsToday';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function newReservationsToday(): Actions\Action
    {
        $newReservationsToday = Reservation::query()->whereDate('created_at', today())->count();
        return Actions\Action::make('onboarding')
            ->modalHeading('Reservations made today')
            ->visible($newReservationsToday > 0)
            ->action(null)
            ->color('info')
            ->modalCancelAction(false)
            ->modalSubmitAction(false)
            ->modalDescription(new HtmlString("Today we received <strong> {$newReservationsToday}</strong> reservations, check now"));
    }
    
    public function getTabs(): array 
    {
      return [
        null => ListRecords\Tab::make('All')
        ->badge($this->getTotalCount()),
        'approved' => ListRecords\Tab::make()->query(fn ($query) => $query->where('status', 'approve'))
        ->badge($this->getCountByStatus('approve')),
        'ongoing' => ListRecords\Tab::make()->query(fn ($query) => $query->where('status', 'ongoing'))
        ->badge($this->getCountByStatus('ongoing')),
        'done' => ListRecords\Tab::make()->query(fn ($query) => $query->where('status', 'done'))
        ->badge($this->getCountByStatus('done')),
        'not appeared' => ListRecords\Tab::make()->query(fn ($query) => $query->where('status', 'not_appeared'))
        ->badge($this->getCountByStatus('not_appeared')),
        'completed' => ListRecords\Tab::make()->query(fn ($query) => $query->where('status', 'completed'))
        ->badge($this->getCountByStatus('completed')),
        ];
    }
    protected function getTotalCount(): int
    {
        return Reservation::count(); 
    }

    protected function getCountByStatus(string $status): int
    {
        return Reservation::where('status', $status)->count(); 
    }
}
