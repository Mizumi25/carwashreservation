<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Filament\Resources\PaymentResource\RelationManagers;
use App\Models\Payment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'fas-wallet';
    
    protected static ?string $navigationLabel = 'Payments';
    
    protected static ?string $modelLabel = 'Payments';
    
    protected static ?int $navigationSort = 4;
    
    protected static ?string $recordTitleAttribute = 'reservation.user.name';
    
    // public static function getGlobalSearchResultTitle(Model $record): string
    // {
    //   return $record->user->name;
    // }
    
    // public static function getGloballySearchableAttributes(): array
    // {
    //     return ['reservation.user.name', 'reservation.service.service_name', 'amount', 'payment_method', 'payment_status'];
    // }
    
    // public static function getGlobalSearchResultDetails(Model $record): array
    // {
    //     return [
    //         'Payment Date' => $record->created_at,
    //         'Amount' => $record->amount, 
    //     ];
    // }
    
    // public static function getGlobalSearchEloquentQuery(): Builder
    // {
    //     return parent::getGlobalSearchEloquentQuery()->with(['user', 'service', 'payment', 'reservation.vehicle.vehicleType']);
    // }
    
    public static function getNavigationBadge(): ?string
    {
      return static::getModel()::count();
    }
    
    public static function getNavigationBadgeColor(): string|array|null
    {
      return static::getModel()::count() > 10 ? 'warning' : 'success';
    }

    public function afterSave()
    {
        $totalAmount = $this->record->reservation->vehicle->vehicleType->price + $this->record->reservation->service->price;
        $paidAmount = $this->record->amount;

        // Update payment status
        if ($paidAmount == $totalAmount) {
            $this->record->payment_status = 'fully_paid';
        } elseif ($paidAmount > 0) {
            $this->record->payment_status = 'partially_paid';
        } else {
            $this->record->payment_status = 'not_paid';
        }

        $this->record->save();  // Save the updated payment status
    }




    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('amount')
                    ->label('Amount to Pay')
                    ->required()
                    ->numeric()
                    ->default(function ($get) {
                        // Calculate remaining amount based on payment status
                        $record = $get('record');  // Get the current payment record
                        $totalAmount = $record->reservation->vehicle->vehicleType->price + $record->reservation->service->price;
                        
                        if ($record->payment_status == 'partialy_paid') {
                            // Subtract the already paid amount from the total
                            return $totalAmount - $record->amount;
                        }
                        
                        return $totalAmount;
                    }),

                    Forms\Components\TextInput::make('total_amount')
    ->label('Total Amount')
    ->disabled()  // Make this field read-only
    ->afterStateHydrated(function ($state, $record) {
        // Populate total_amount with the service price
        if ($record && $record->reservation && $record->reservation->service) {
            $state->state($record->reservation->service->price);
        }
    }),

                

                // You can include other relevant fields, such as payment method, etc.
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('Payment ID')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('created_at')->label('Date')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('reservation.user.name')->label('User Name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('reservation.service.service_name')->label('Service')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('reservation.vehicle_type_name')->label('Vehicle Type')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('payment_status')->badge()
                ->color(function (string $state): string
                {
                  return match ($state) {
                    'partialy_paid' => 'info',
                    'fully_paid' => 'success',
                    'not_paid' => 'warning',
                    };
                  }
                 ),
                Tables\Columns\TextColumn::make('payment_method')->label('Payment Method')->searchable()->sortable(), 
                Tables\Columns\TextColumn::make('amount')->label('Total Amount')->prefix('P')->searchable()->sortable(),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('pay')
                    ->label('Pay')
                    ->action(function ($record) {
                        return redirect()->route('filament.admin.resources.payments.edit', $record->id);
                    })
                    ->visible(function ($record) {
                        return in_array($record->payment_status, ['not_paid', 'partialy_paid']);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'view' => Pages\ViewPayment::route('/{record}'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }

    public static function getWidgets(): array {
        return [
            PaymentResource\Widgets\TotalIncomeWidget::class,
        ];
    }
}
