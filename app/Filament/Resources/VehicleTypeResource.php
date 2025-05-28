<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VehicleTypeResource\Pages;
use App\Filament\Resources\VehicleTypeResource\RelationManagers;
use App\Models\VehicleType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\Layout\Grid;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\MarkdownEditor;

class VehicleTypeResource extends Resource
{
    protected static ?string $model = VehicleType::class;

    protected static ?string $navigationIcon = 'fas-motorcycle';
    
    protected static ?string $navigationLabel = 'Vehicle Type';
    
    protected static ?string $modelLabel = 'Manage Vehicle Type';
    
    protected static ?string $navigationGroup = 'Manage';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->required(),
                Forms\Components\MarkdownEditor::make('description')->required(),
                Forms\Components\FileUpload::make('icon')->disk('public') ->directory('vehicle_type_icons'),
                Forms\Components\TextInput::make('price')->numeric()->prefix('P')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
          ->columns([
              Panel::make([
                  Stack::make([
                      ImageColumn::make('icon')
                          ->label('Icon')
                          ->grow(false)
                          ->extraAttributes([
                              'class' => 'plcae-items-center mx-auto mb-4 w-[20vw] h-[20vh]' 
                          ]),
                      TextColumn::make('name')->searchable()
                          ->label('Vehicle Name')
                          ->weight('bold')
                          ->extraAttributes(['class' => 'text-center']), 
                      TextColumn::make('description')
                          ->label('Description')
                          ->wrap()
                          ->extraAttributes(['class' => 'text-center mt-2']),
                      TextColumn::make('price')->searchable()
                          ->label('Price')
                          ->money('php')
                          ->extraAttributes(['class' => 'text-center mt-2']),
                  ])->extraAttributes(['class' => 'space-y-4']),  
              ])
              ->collapsible(false)
              ->extraAttributes(['class' => 'p-6']) 
          ])
          ->contentGrid([
              'md' => 2,
              'xl' => 3,
              'gap' => 6, 
          ])
          ->actions([
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVehicleTypes::route('/'),
            'create' => Pages\CreateVehicleType::route('/create'),
            'edit' => Pages\EditVehicleType::route('/{record}/edit'),
        ];
    }
}
