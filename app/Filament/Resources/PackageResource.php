<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PackageResource\Pages;
use App\Filament\Resources\PackageResource\RelationManagers;
use App\Models\Package;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\MultiSelect;
use Filament\Forms\Components\MarkDownEditor;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Service;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists\Components\Card;
use Filament\Tables\Columns\Layout\Grid;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;

class PackageResource extends Resource
{
    protected static ?string $model = Package::class;

    protected static ?string $navigationIcon = 'fas-cube';
    
    protected static ?string $navigationLabel = 'Package';
    
    protected static ?string $modelLabel = 'Manage Package';
    
    protected static ?string $navigationGroup = 'Manage';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->label('Package Name'),
                MarkdownEditor::make('description')->required()->columnSpanFull()
                    ->placeholder('Enter a brief description'),
                MultiSelect::make('services')
                    ->relationship('services', 'service_name')
                    ->required()
                    ->maxItems(5)
                    ->reactive()
                    ->options(Service::all()->pluck('service_name', 'id'))
                    ->helperText('Select up to 5 services.')
                    ->rules(['array', 'max:5'])
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        $selectedServiceIds = $state ?? [];
                        $totalPrice = Service::whereIn('id', $selectedServiceIds)->sum('price');
                        $set('original_price', number_format($totalPrice, 2, '.', ''));

                        $discount = $get('discount') ?? 0;
                        $discountedPrice = $totalPrice * (1- $discount / 100);
                        $set('discounted_price', number_format($discountedPrice, 2, '.', ''));
                    })->dehydrated(fn ($state) => $state ? $state : []),
                TextInput::make('original_price')
                    ->readonly()
                    ->reactive()
                    ->label('Original Price')
                    ->default(0)
                    ->prefix('P')
                    ->numeric()
                    ->helperText('Original Price calculated based on selected Services')
                    ->afterStateUpdated(function ($state, callable $set) {
                        $set('original_price', number_format($state, 2, '.', ''));
                    }),
                TextInput::make('discount')
                    ->numeric()
                    ->label('Discount (%)')
                    ->helperText('Enter discount rate')
                    ->reactive()
                    ->required()
                    ->suffix('%')
                    ->afterStateUpdated(function (callable $get, callable $set) {
                        $originalPrice = (float)$get('original_price');
                        $discount = (float)$get('discount');
                        $discountedPrice = $originalPrice * (1 - $discount / 100);
                        $set('discounted_price', number_format($discountedPrice, 2, '.', ''));
                    }),
               TextInput::make('discounted_price')
                    ->label('Discounted Price')
                    ->disabled()
                    ->prefix('P')
                    ->reactive()
                    ->helperText('Calculated Final Price')
                    ->hidden(fn (callable $get) => $get('original_price') == 0 || $get('discount') == 0),
                TextInput::make('duration')
                    ->numeric()
                    ->required()
                    ->label('Duration'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            Panel::make([
                Stack::make([
                    TextColumn::make('name')->searchable()
                        ->label('Package Name')
                        ->weight('bold')
                        ->extraAttributes(['class' => 'text-center']), 
                    TextColumn::make('description')
                        ->label('Description')
                        ->wrap()
                        ->extraAttributes(['class' => 'text-center mt-2']),
                    TextColumn::make('original_price')->searchable()
                        ->label('Original Price')
                        ->money('php')
                        ->extraAttributes(['class' => 'text-center mt-2']),
                        TextColumn::make('services')
                        ->label('Services')
                        ->getStateUsing(function ($record) {
                            return $record->services->pluck('service_name')->implode(', ');
                        })
                        ->extraAttributes(['class' => 'text-center mt-2']),
                    TextColumn::make('discounted_price')
                        ->label('Discounted Price')
                        ->getStateUsing(function ($record) {
                            return $record->discounted_price;
                        })
                        ->money('php')
                        ->extraAttributes(['class' => 'text-center mt-2']),
                ])->extraAttributes(['class' => 'space-y-4']),  
            ])
            ->collapsible(false)
            ->extraAttributes(['class' => 'p-6']) 
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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

    public static function afterSave(Form $form, $record): void
    {
        $selectedServices = $form->getState()['services'] ?? [];

        $record->services()->sync($selectedServices);
    }



    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPackages::route('/'),
            'create' => Pages\CreatePackage::route('/create'),
            'view' => Pages\ViewPackage::route('/{record}'),
            'edit' => Pages\EditPackage::route('/{record}/edit'),
        ];
    }
}
