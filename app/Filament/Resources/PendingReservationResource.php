<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PendingReservationResource\Pages;
use App\Filament\Resources\PendingReservationResource\RelationManagers;
use App\Models\Reservation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use App\Mail\ReservationApprovedMail;
use App\Mail\ReservationDeclinedMail;
use Illuminate\Support\Facades\Mail;

class PendingReservationResource extends Resource
{
    protected static ?string $model = Reservation::class;

    protected static ?string $navigationIcon = 'fas-spinner';
    
    protected static ?string $navigationLabel = 'Pending';
    
    protected static ?string $modelLabel = 'Pending Reservations';
    
    protected static ?string $navigationParentItem = 'Reservations';
    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count(); 
    }
    
    public static function getNavigationBadgeColor(): string|array|null
    {
      return static::getModel()::where('status', 'pending')->count() > 10 ? 'warning' : 'success';
    }
    
    
    

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
    return $table
        ->query(Reservation::query()->where('status', 'pending')) 
        ->columns([
            Tables\Columns\TextColumn::make('id')->label('Reservation ID')->searchable(),
            Tables\Columns\TextColumn::make('user.name')->label('User Name')->searchable(),
            Tables\Columns\TextColumn::make('service.service_name')->label('Service')->searchable(),
            Tables\Columns\TextColumn::make('package.name')->label('Package')->searchable(),
            Tables\Columns\TextColumn::make('vehicle_type_name')->label('Vehicle Type')->searchable(),
            Tables\Columns\TextColumn::make('schedule.date') 
                ->label('Reservation Date')->searchable(),         
            Tables\Columns\TextColumn::make('schedule.time_slot') 
                ->label('Time Slot')->searchable(),                  
            Tables\Columns\TextColumn::make('reservation_date')->label('Made')->searchable(),
            Tables\Columns\TextColumn::make('status')->label('Status'),
        ])
                 ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('approve')
                ->label('Approve')
                ->action(function (Reservation $record) {
                    $record->update(['status' => 'approve']);
                    
                    broadcast(new \App\Events\ReservationStatusUpdated($record));

                    Notification::make()
                      ->title('Reservation Approved')
                      ->success()
                      ->body(
                            'Name: ' . $record->user->name . 
                            '. Reservation ID: ' . $record->id . 
                            '. Reserved Item: ' . ($record->service->service_name ?? $record->package->name) . 
                            '. Reservation was approved!'
                       )
                      ->actions([
                          \Filament\Notifications\Actions\Action::make('Continue')
                              ->button()
                              ->color('info')
                              ->url(route('reservation.continue', [
                                  'id' => $record->id,
                                  'service_name' => $record->service->service_name ?? $record->package->name,
                              ]), shouldOpenInNewTab: false)
                              ->extraAttributes([
                                  'class' => 'bg-blue-500 hover:bg-blue-600'
                              ])
                      ])
                    ->sendToDatabase($record->user);

                    Mail::to($record->user->email)->send(new ReservationApprovedMail($record));
                    
                    Notification::make()
                        ->title('Success')
                        ->body('Reservation approved successfully!')
                        ->success() 
                        ->send();
                })
                ->color('success'),
                Tables\Actions\Action::make('decline')
                    ->label('Decline')
                    ->modalHeading('Decline Reservation')
                    ->modalSubheading('Please provide a reason for declining the reservation.')
                    ->form([
                         Forms\Components\TextInput::make('decline_message')
                            ->label('Decline Message')
                            ->required(),
                    ])
                    ->action(function (Reservation $record, array $data) {
                        $record->update([
                            'status' => 'decline',
                            'decline_message' => $data['decline_message'],
                        ]);

                        Mail::to($record->user->email)->send(new ReservationDeclinedMail($record));
                        
                        Notification::make()
                        ->title('Your Reservation has been declined')
                        ->body(
                            'Name: ' . $record->user->name . 
                            '. Reservation ID: ' . $record->id . 
                            '. Reserved Item: ' . ($record->service->service_name ?? $record->package->name) . 
                            '. Reason: ' . $data['decline_message']
                        )
                        ->sendToDatabase($record->user);
                        
                        
                        $record->delete();
                        
                        Notification::make()
                            ->title('Success')
                            ->body('Reservation declined successfully!')
                            ->warning() 
                            ->send();
                            
                            
                    })
                    ->color('danger'),
            ])
            ->searchable();
            broadcast(new \App\Events\ReservationDeclined([
                'user_id' => $record->user->id,
                'reservation_id' => $record->id,
                'decline_message' => $data['decline_message'],
            ]));           
            broadcast(new \App\Events\ReservationApproved([
                'user_id' => $record->user->id,
                'reservation_id' => $record->id,
                'service_name' => $record->service->service_name,
            ]));             
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManagePendingReservations::route('/'),
        ];
    }
    public static function canCreate(): bool
    {
        return false; 
    }
}



