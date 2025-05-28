<?php

use Livewire\Volt\Component;
use App\Models\Vehicle;
use App\Models\VehicleType;

new class extends Component {
    public $vehicleTypes = [];
    public $selectedVehicleTypeId;
    public $model = '';
    public $make = '';
    public $year = '';
    public $license_plate = '';
    public $color = '';
    public $vehicles;
    public $selectedVehicleId = null;
    public $bgColor;
    
    public function mount(): void 
    {
        $this->vehicleTypes = VehicleType::select('id', 'name', 'description', 'price', 'icon')->get() ?? collect(); 
        $this->vehicles = Vehicle::with('vehicleType')->where('user_id', auth()->id())->get() ?? collect();

        foreach ($this->vehicles as $vehicle) {
            $vehicle->bgColor = $this->getRandomColor();
        }
    }

    public function getRandomColor()
    {
        $colors = ['#FFC0CB', '#87CEEB', '#FFA500', '#FFFF00']; 
        return $colors[array_rand($colors)];
    }

        
    public function selectVehicleType($vehicleTypeId)
    {
        $this->selectedVehicleTypeId = $vehicleTypeId;
        \Log::info('Selected Vehicle Type ID: ' . $this->selectedVehicleTypeId);
    }

    public function selectVehicle($vehicleId)
    {
        $vehicle = Vehicle::find($vehicleId);
        if ($vehicle) {
            $this->selectedVehicleId = $vehicleId; 
            $this->selectedVehicleTypeId = $vehicle->vehicle_type_id; 
            $this->model = $vehicle->model;
            $this->make = $vehicle->make;
            $this->year = $vehicle->year;
            $this->license_plate = $vehicle->license_plate;
            $this->color = $vehicle->color;
        }

        foreach ($this->vehicles as $vehicle) {
            $vehicle->bgColor = $this->getRandomColor();
        }
    }

    public function selectVehicleDelete($vehicleId)
    {
        $vehicle = Vehicle::find($vehicleId);

        if ($vehicle) {
            $vehicle->delete();

            $this->vehicles = $this->vehicles->filter(function ($v) use ($vehicleId) {
                return $v->id !== $vehicleId;
            });
            if ($this->selectedVehicleId === $vehicleId) {
                $this->cancelEdit(); 
            }

            session()->flash('message', 'Vehicle deleted successfully!');
        } else {
            session()->flash('error', 'Vehicle not found!');
        }

        foreach ($this->vehicles as $vehicle) {
            $vehicle->bgColor = $this->getRandomColor();
        }
    }



    public function cancelEdit()
    {
        $this->reset(['model', 'make', 'year', 'license_plate', 'color', 'selectedVehicleTypeId', 'selectedVehicleId']);
    }

    public function submitVehicle()
    {
        $this->validate([
            'selectedVehicleTypeId' => 'required|exists:vehicle_types,id',
            'model' => 'required|string',
            'make' => 'required|string',
            'year' => 'required|integer',
            'license_plate' => 'required|string|unique:vehicles,license_plate,' . $this->selectedVehicleId,
            'color' => 'required|string',
        ]);
    
        if ($this->selectedVehicleId) {
            // Update existing vehicle
            $vehicle = Vehicle::find($this->selectedVehicleId);
            if (!$vehicle) {
                \Log::error('Vehicle not found for ID: ' . $this->selectedVehicleId);
                session()->flash('error', 'Vehicle not found!');
                return;
            }
    
            $vehicle->update([
                'vehicle_type_id' => $this->selectedVehicleTypeId,
                'model' => $this->model,
                'make' => $this->make,
                'year' => $this->year,
                'license_plate' => $this->license_plate,
                'color' => $this->color,
            ]);
            session()->flash('message', 'Vehicle updated successfully!');
        } else {
            
            Vehicle::create([
                'user_id' => auth()->id(),
                'vehicle_type_id' => $this->selectedVehicleTypeId, 
                'model' => $this->model,
                'make' => $this->make,
                'year' => $this->year,
                'license_plate' => $this->license_plate,
                'color' => $this->color,
            ]);
            session()->flash('message', 'Vehicle added successfully!');
        }
    
        $this->vehicles = Vehicle::with('vehicleType')->where('user_id', auth()->id())->get() ?? collect();
    
        $this->reset(['model', 'make', 'year', 'license_plate', 'color', 'selectedVehicleTypeId', 'selectedVehicleId']);
        foreach ($this->vehicles as $vehicle) {
            $vehicle->bgColor = $this->getRandomColor();
        }
    }
    
    
    public function getIsSubmitEnabledProperty()
    {
        return !empty($this->selectedVehicleTypeId) &&
               !empty($this->model) &&
               !empty($this->make) &&
               !empty($this->year) &&
               !empty($this->license_plate) &&
               !empty($this->color);
    }
}; ?>


<div>
    <div class='w-[95vw] place-items-start items-start grid grid-cols-2 gap-0'>
        <div class="p-7 px-[30px] {{ $mode === 'dark' ? 'bg-[#313246] text-white' : 'bg-white text-black' }} shadow-lg sm:rounded-lg w-[90%] pb-[8rem] rounded-[12px] overflow-auto h-[100vh] transition-all duration-300">
            <form wire:submit.prevent="submitVehicle">
                <div class="w-full">
                    <section class="px-6 py-8">
                        <header>
                            <p class="mt-1 text-sm text-gray-600">{{ __("Select type and input details") }}</p>
                        </header>

                        <h3 class="text-xl font-semibold text-gray-900 mb-6">Available Vehicle Types</h3>
                        <div class="w-full">
                            <div class="grid grid-cols-2 gap-6 h-[60vh] overflow-y-auto">
                                @foreach ($vehicleTypes as $type)
                                    <button type="button" 
                                        class="w-full p-4 mb-4 rounded-lg transition-all duration-200 ease-in-out
                                               {{ $selectedVehicleTypeId === $type->id ? 'bg-blue-400 text-white shadow-lg' : 'bg-gray-100 text-gray-800 hover:bg-gray-200' }}"
                                        wire:click="selectVehicleType({{ $type->id }})">
                                        <div class="flex flex-col items-center">
                                            <img src="{{ asset('storage/' . $type->icon) }}" class="h-full w-full mb-3" alt="VehicleTypeIcon" />
                                            <p class="text-sm"><strong>Name:</strong> {{ $type->name }}</p>
                                        </div>
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <div class="vehicleInput mt-6 space-y-6">
                            <div>
                                <label for="model" class="block text-sm font-medium text-gray-900">Model</label>
                                <input wire:model.debounce.500ms="model" placeholder="Enter Model" type="text" id="model-input" class="bg-gray-50 border border-gray-300 text-sm rounded-lg block w-full p-3 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <div>
                                <label for="make" class="block text-sm font-medium text-gray-900">Make</label>
                                <input wire:model.debounce.500ms="make" placeholder="Enter Make" type="text" id="make-input" class="bg-gray-50 border border-gray-300 text-sm rounded-lg block w-full p-3 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <div class="grid grid-cols-2 gap-8">
                                <div>
                                    <label for="color" class="block text-sm font-medium text-gray-900">Color</label>
                                    <div class="flex items-center">
                                        <input wire:model.debounce.500ms="color" type="color" id="color-input" class="cursor-pointer w-10 h-10 border-0 rounded-full">
                                        <input wire:model.debounce.500ms="color" placeholder="Enter Color" type="text" id="color-text-input" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 w-full p-3 ml-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                                    </div>
                                </div>
                                <div>
                                    <label for="license_plate" class="block text-sm font-medium text-gray-900">License Plate Number</label>
                                    <input wire:model.debounce.500ms="license_plate" placeholder="e.g., ABC1234" type="text" id="license_plate" class="bg-gray-50 border border-gray-300 text-sm rounded-lg block w-full p-3 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                                    <p class="mt-1 text-xs text-gray-500">Please enter the vehicle's license plate number.</p>
                                </div>
                            </div>
                            <div>
                                <label for="year" class="block text-sm font-medium text-gray-900">Year</label>
                                <div class="relative w-full">
                                    <select wire:model.debounce.500ms="year" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 w-full p-3 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                                        <option value="">Select Year</option>
                                        @for ($i = date('Y'); $i >= 1970; $i--)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>

                <div class="flex justify-between mt-8">
                    <button :disabled="!$this->isSubmitEnabled" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200">
                        {{ __('Save or Update') }}
                    </button>
                    <button wire:click="cancelEdit" type="button" class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-all duration-200">
                        Cancel
                    </button>
                </div>
            </form>

            @if (session()->has('message'))
                <div class="mt-4 text-green-600">
                    {{ session('message') }}
                </div>
            @endif
        </div>

        <section class="mt-12">
            <div class="px-1">
                <div class="bg-gray-50 dark:bg-gray-800 relative w-[25rem] shadow-lg sm:rounded-lg overflow-hidden">
                    <div class="overflow-x-auto">
                        <div class="grid grid-cols-1 gap-4">
                            @foreach($vehicles as $vehicle)
                                <div class="bg-white shadow-md rounded-lg p-4 w-full flex flex-row justify-between items-center relative">
                                    <div class="w-[10px] h-full absolute left-0 top-0 z-0 rounded-full" style="background-color: {{ $vehicle->bgColor }};"></div>
                                    <div class="flex flex-col flex-start text-sm text-gray-700 ml-5">
                                        <h2 class="text-lg font-semibold mb-2">Vehicle Details</h2>
                                        <p><strong>Vehicle Type:</strong> {{ $vehicle->vehicleType->name ?? 'N/A' }}</p>
                                        <p><strong>Model:</strong> {{ $vehicle->model }}</p>
                                        <p><strong>Make:</strong> {{ $vehicle->make }}</p>
                                        <p><strong>Year:</strong> {{ $vehicle->year }}</p>
                                        <p><strong>License Plate:</strong> {{ $vehicle->license_plate }}</p>
                                        <p><strong>Color:</strong> {{ $vehicle->color }}</p>
                                    </div>
                                    <div class="flex space-x-4 absolute right-2 bottom-4">
                                        <button wire:click="selectVehicle({{ $vehicle->id }})" class="px-3 py-1 bg-[#5186E8] text-white rounded-lg" title="Edit Vehicle">
                                            <i class="fa-solid fa-pen-nib"></i>
                                        </button>
                                        <button wire:click="selectVehicleDelete({{ $vehicle->id }})" class="px-3 py-1 bg-[#e85163] text-white rounded-lg" title="Delete Vehicle">
                                            <i class="fa-solid fa-trash-can-arrow-up"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <style>
        [disabled] {
            opacity: 0.6;
            cursor: not-allowed;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const figure = document.querySelectorAll('.sliderItem figure');
            figure.forEach(item => {
                item.addEventListener('click', function() {
                    figure.forEach(i => {
                        i.classList.remove('selected');
                        i.style.transform = 'scale(1)';
                    });
                    this.classList.add('selected');
                });
            });
        });
    </script>
</div>
