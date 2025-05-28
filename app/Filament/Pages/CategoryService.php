<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Section;
use Filament\Tables\Actions;
use Filament\Notifications\Notification;
use App\Models\Category;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\Action;

class CategoryService extends Page implements HasTable
{
    use \Filament\Tables\Concerns\InteractsWithTable;

    protected static ?string $navigationIcon = 'fas-list';
    
    protected static string $view = 'filament.pages.category-service';
    
    protected static ?string $navigationLabel = 'Category';
    
    protected static ?string $modelLabel = 'Manage Categories';
    
    protected static ?string $navigationGroup = 'Manage';
    
    protected static ?int $navigationSort = 4;
    
    public ?array $data = [];
    public ?Category $categoryToUpdate = null;

    public function mount(): void
    {
        if ($this->categoryToUpdate) {
            $this->form->fill([
                'name' => $this->categoryToUpdate->name,
                'description' => $this->categoryToUpdate->description,
            ]);
        }
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        TextInput::make('name')->required(),
                        Textarea::make('description'),
                    ]),
            ])->statePath('data');
    }

    // Use this method to handle both create and update operations
    public function save(): void
    {
        if ($this->categoryToUpdate) {
            $this->categoryToUpdate->update($this->form->getState());

            Notification::make()->title('Category Updated')->success()->send();

            $this->categoryToUpdate = null; // Clear after update
            $this->form->fill([]);  // Clear form after update
        } else {
            Category::create($this->form->getState());

            Notification::make()->title('Category Created')->success()->send();

            $this->form->fill([]);  // Reset form after creation
        }
    }

    // Add this `create` method if Filament expects it
    public function create(): void
    {
        $this->save();  // Call save method when create is triggered
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Category::query())  
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('description')->default('-')->limit(50),
            ])
            ->filters([ 
                // Add filters if needed
            ])
            ->actions([ 
                Action::make('update')
                    ->label('Update')
                    ->action(function (Category $record) {
                        $this->categoryToUpdate = $record;
                        $this->form->fill([
                            'name' => $record->name,
                            'description' => $record->description,
                        ]);
                    })
                    ->color('primary'),
               DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ])
            ]);
    }
}
