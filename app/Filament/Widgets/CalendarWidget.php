<?php

namespace App\Filament\Widgets;

use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use Saade\FilamentFullCalendar\Data\EventData;
use App\Models\Reservation;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms;
use App\Filament\Resources\RescheduleReservationResource;
use Saade\FilamentFullCalendar\Actions;

class CalendarWidget extends FullCalendarWidget
{
    public Model | string | null $model = Reservation::class;

    public function fetchEvents(array $fetchInfo): array
    {
        return Reservation::query()
            ->where('status', 'approve')
            ->whereHas('schedule', function ($query) use ($fetchInfo) {
                $query->where('date', '>=', $fetchInfo['start'])
                      ->where('date', '<=', $fetchInfo['end']);
            })
            ->with(['schedule', 'user']) 
            ->get()
            ->map(
                fn (Reservation $event) => EventData::make()
                    ->id($event->id)
                    ->title($event->user->name)
                    ->start($event->schedule->date) 
                    ->end($event->schedule->date) 
                    ->url(
                        url: RescheduleReservationResource::getUrl(name: 'view', parameters: ['record' => $event]),
                        shouldOpenUrlInNewTab: true
                    )
            )
            ->toArray();
    }

    public function getFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('status')
                ->label('Status')
                ->disabled(),
            Forms\Components\TextInput::make('user_name')
                ->label('User Name')
                ->disabled(),
            Forms\Components\TextInput::make('serve')
                ->label('Service')
                ->disabled(),
            Forms\Components\DatePicker::make('schedule_date') 
                ->label('Schedule Date')
                ->required(), 
            Forms\Components\TimePicker::make('time_slot') 
                ->label('Time Slot')
                ->required(), 
        ];
    }

    protected function modalActions(): array
    {
        return [
            Actions\EditAction::make()
                ->mountUsing(
                    function (Reservation $record, Forms\Form $form, array $arguments) {
                        $form->fill([
                            'status' => $record->status,
                            'user_name' => $record->user->name,
                            'serve' => $record->service->service_name ?? $record->package->name,
                            'schedule_date' => $arguments['event']['start'] ?? $record->schedule->date,
                            'time_slot' => $record->schedule->time_slot,
                        ]);
                    }
                )
                ->action(function (Reservation $record, array $data) {
                    $record->schedule->update([
                        'date' => $data['schedule_date'], 
                        'time_slot' => $data['time_slot'], 
                    ]);
                }),
            Actions\DeleteAction::make(),
        ];
    }
}