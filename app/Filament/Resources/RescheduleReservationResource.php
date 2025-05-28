<?php

namespace App\Filament\Resources;

use App\Models\Reservation;
use App\Filament\Resources\RescheduleReservationResource\Pages;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms\Form;
use App\Filament\Widgets\CalendarWidget;

class RescheduleReservationResource extends Resource
{
    protected static ?string $model = Reservation::class;
    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('user.name')->label('Name'),
                Forms\Components\DateTimePicker::make('schedule.date'),
            ]);
    }

  

    public static function getPages(): array
{
    return [
        'index' => Pages\ListRescheduleReservations::route('/'),
        'create' => Pages\CreateRescheduleReservation::route('/create'),
        'view' => Pages\ViewRescheduleReservation::route('/{record}'),
        'edit' => Pages\EditRescheduleReservation::route('/{record}/edit'),
    ];
}



    public static function index(Request $request)
    {
        return parent::index($request)
            ->withWidgets([
                CalendarWidget::class,  
            ]);
    }


}
