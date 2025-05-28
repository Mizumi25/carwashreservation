<?php

use Livewire\Volt\Component;
use App\Models\Reservation;
use App\Models\Service;
use App\Models\Package;
use Masmerise\Toaster\Toaster;

new class extends Component {
    public $reservations;
    public $services;
    public $packages;
    public $selectedService = '';
    public $selectedPackage = '';
    public $searchTerm = '';
    public $completedReservations;

    public $packageImages = [
        'package1.png',
        'package2.png',
        'package3.png',
      ];

    public function mount()
    {
        $this->services = Service::all();
        $this->packages = Package::all();
        $this->reservations = Reservation::with(['vehicle', 'service', 'package', 'schedule'])
                ->where('user_id', auth()->id()) 
                ->whereNotIn('status', ['cancelled', 'decline', 'completed', 'not_appeared'])  
                ->get();
        $this->completedReservations = Reservation::with(['vehicle', 'service', 'package', 'schedule'])
                ->where('user_id', auth()->id())
                ->where('status', 'completed')
                ->get();
        $this->showCancelModal = false;
        
        $this->packageImages = array_map(function($image) {
            return asset('storage/package_icons/' . $image);
        }, $this->packageImages);
    }


    public function updatedSelectedService($value)
    {
        if ($value) {
            $this->selectedPackage = '';
        }
        $this->filterReservations();
    }
    
    public function updatedSelectedPackage($value)
    {
        if ($value) {
            $this->selectedService = '';
        }
        $this->filterReservations();
    }


    public function updatedSearchTerm()
    {
        $this->filterReservations();
    }

    public function toggleReservationSelection($reservationId)
    {
        if (in_array($reservationId, $this->selectedReservations)) {
            $this->selectedReservations = array_filter(
                $this->selectedReservations,
                fn($id) => $id !== $reservationId
            );
        } else {
            $this->selectedReservations[] = $reservationId;
        }
    }


    private function filterReservations()
    {
        $this->reservations = Reservation::query()
            ->with(['vehicle', 'service', 'package', 'schedule'])
            ->where('user_id', auth()->id()) 
            ->when($this->selectedService, function ($query) {
                $query->where('service_id', $this->selectedService);
            })
            ->when($this->selectedPackage, function ($query) { 
                $query->where('package_id', $this->selectedPackage);
            })
            ->when($this->searchTerm, function ($query) {
                $query->where(function ($query) {
                    $query->whereHas('vehicle', function ($q) {
                        $q->whereHas('vehicleType', function ($qt) {
                            $qt->where('name', 'like', '%' . $this->searchTerm . '%');
                        });
                    })
                    ->orWhereHas('service', function ($q) {
                        $q->where('service_name', 'like', '%' . $this->searchTerm . '%');
                    })
                    ->orWhereHas('package', function ($q) { 
                        $q->where('name', 'like', '%' . $this->searchTerm . '%');
                    })
                    ->orWhereDate('reservation_date', '=', $this->searchTerm)
                    ->orWhereHas('schedule', function ($q) {
                        $q->where('time_slot', 'like', '%' . $this->searchTerm . '%');
                    });
                });
            })
            ->whereNotIn('status', ['cancelled', 'decline', 'completed'])
            ->get();
        
        Log::info('Reservations filtered: ' . count($this->reservations));
    }

    public function continueReservation($reservationId)
    {   
        $reservation = Reservation::find($reservationId);
        $serviceName = $reservation->service->service_name ?? '';
        $packageName = $reservation->package->name ?? '';
        
        return redirect()->route('reservation.continue', [
            'id' => $reservationId,
            'service_name' => $serviceName ?: $packageName, 
        ]);
    }

    public $reservationIdToCancel = null;
    public $showCancelModal = false;

    public function confirmCancelReservation($reservationId)
    {
        $this->reservationIdToCancel = $reservationId;
        $this->showCancelModal = true;
    }

    public function cancelReservation()
    {
        $reservation = Reservation::find($this->reservationIdToCancel);
        if ($reservation) {
            $reservation->status = 'cancelled';
            $reservation->save();
        }

        $this->showCancelModal = false;
        $this->reservationIdToCancel = null;

        Toaster::warning('Reservation Cancelled');

        $this->filterReservations();
}

public $selectedReservations = [];

public function selectAllReservations()
{
    if (count($this->selectedReservations) == count($this->reservations)) {
        $this->selectedReservations = [];
    } else {
        $this->selectedReservations = $this->reservations->pluck('id')->toArray();
    }
}

public function cancelAllReservations()
{
    Reservation::whereIn('id', $this->selectedReservations)
        ->update(['status' => 'cancelled']);

    $this->selectedReservations = [];

    Toaster::warning('Reservation Cancelled');
    $this->filterReservations(); 
}




};
?>

<div>
    <div x-data="{ show: @entangle('showCancelModal') }" x-show="show" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" style="display: none;">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Confirm Cancellation</h2>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                Are you sure you want to cancel this reservation? This action cannot be undone.
            </p>
            <div class="mt-4 flex justify-end space-x-4">
                <button @click="show = false" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400 text-gray-800">
                    Close
                </button>
                <button wire:click="cancelReservation" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                    Confirm
                </button>
            </div>
        </div>
    </div>

    <section class="mt-3">
        <div class="w-[78vw]">
            <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden">
                <div class="flex items-center justify-between d p-4">
                    <div class="flex">
                        <div class="relative w-full">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg aria-hidden="true" class="w-5 h-5 text-gray-500 dark:text-gray-400"
                                    fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input  type="text" wire:model.live="searchTerm"
                                class="bg-white border-b border-gray-300 text-gray-500 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 p-2 "
                                placeholder="Search" required="">
                        </div>
                    </div>

                    




                    <div class="flex space-x-3">
                        <div class="flex space-x-3 items-center">
                            <label class="w-40 text-sm font-medium text-gray-500">Service :</label>
                            <select wire:model.live="selectedService"
                                class="bg-gray-50 border border-gray-300 text-gray-500 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 ">
                                <option value="">All Services</option>
                                @foreach($services as $service)
                                    <option value="{{ $service->id }}">{{ $service->service_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex space-x-3 items-center">
                            <label class="w-40 text-sm font-medium text-gray-500">Package :</label>
                            <select wire:model.live="selectedPackage"
                                class="bg-gray-50 border border-gray-300 text-gray-500 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 ">
                                <option value="">All Packages</option>
                                @foreach($packages as $package)
                                    <option value="{{ $package->id }}">{{ $package->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if (count($selectedReservations) > 0)
                        <div x-data="{ open: false }" class="relative flex flex-row space-x-1 justify-center items-center">
                            <button @click="open = !open" class="flex">
                                <i class="fa fa-ellipsis-v"></i> 
                            </button>
                            <div 
                                x-show="open" 
                                @click.away="open = false" 
                                class="absolute right-0 mt-2 w-48 bg-white border border-gray-300 rounded-lg shadow-lg z-50" 
                                x-cloak
                            >
                                <button @click="open = false; $wire.cancelAllReservations()" class="flex items-center text-red-500 p-2 text-sm w-full text-left hover:bg-gray-100">
                                    <i class="fa fa-trash mr-2"></i> Cancel Reservations
                                </button>
                            </div>
                            <label class="text-gray-600 text-[13px]">
                                <input type="checkbox" 
                                    wire:click="selectAllReservations" 
                                    :checked="selectedReservations.length === {{ $reservations->count() }}">
                                Select All
                            </label>
                        </div>
                        @endif
                        
                            
                        
                    </div>
                </div>
                <div class="overflow-x-auto h-auto flex justify-start flex-col items-center">
                @if ($reservations->isNotEmpty())
                    
                       
                    <div class="grid grid-cols-3 place-items-center gap-x-5 h-full">
                        @foreach($reservations as $reservation)
                             @php
                                $servicePrice = $reservation->service->price ?? 0;
                                $packagePrice = ($reservation->package && $reservation->package->original_price && $reservation->package->discount)
                                    ? ($reservation->package->original_price * (1 - $reservation->package->discount / 100))
                                    : 0;
                                $vehicleTypePrice = $reservation->vehicle->vehicleType->price ?? 0;
                                $totalAmount = $servicePrice + $vehicleTypePrice + $packagePrice;
                                $randomImage = $this->packageImages[array_rand($this->packageImages)];
                                $isSelected = in_array($reservation->id, $selectedReservations);
                            @endphp
                            
                            <div class="shadow-lg h-[25rem] w-[20rem] p-5 rounded-3xl relative flex flex-col {{ $isSelected ? 'bg-blue-300 text-white' : 'bg-transparent' }} cursor-pointer transition-transform duration-300 transform hover:scale-105" wire:click="toggleReservationSelection({{ $reservation->id }})">
                                <div class="w-full flex flex-row space-x-3 pt-2">
                                    <img class='h-[30px] w-[30px] object-cover rounded-full' src="{{ asset('storage/' . $reservation->user->profile_picture) }}" alt="">
                                    <h1 class="{{ $isSelected ? 'text-white' : 'text-slate-800 ' }} text-[15px] font-bold">{{ $reservation->user->name }}</h1>
                                </div>
                                <div class="absolute right-2 top-6">
                                    <h1 class="rounded-full text-[14px] px-3 py-1 w-[100%] 
                                            @if($reservation->status === 'pending')
                                                bg-orange-200 text-orange-500
                                            @elseif($reservation->status === 'approve')
                                                bg-blue-200 text-blue-500
                                            @elseif($reservation->status === 'ongoing')
                                                bg-yellow-200 text-yellow-500
                                            @elseif($reservation->status === 'in_progress')
                                                bg-violet-200 text-violet-500
                                            @elseif($reservation->status === 'done')
                                                bg-green-200 text-green-500
                                            @endif
                                        ">
                                            {{ $reservation->status }}
                                    </h1>
                                </div>
                                <div class="grid grid-cols-2 place-items-center text-[13px] mt-4">
                                    <div class="flex flex-col items-start justify-center">
                                        <span class="{{ $isSelected ? 'text-white' : 'text-gray-500 ' }}">No: </span>
                                        <h2 class="{{ $isSelected ? 'text-white' : 'text-gray-800 ' }} font-bold">{{ $reservation->id }}</h2>
                                    </div>
                                    <div class="flex flex-col items-start justify-center">
                                        <span class="{{ $isSelected ? 'text-white' : 'text-gray-500 ' }}">Total Payment: </span>
                                        <p class="{{ $isSelected ? 'text-white' : 'text-gray-800 ' }} font-bold">P{{ number_format($totalAmount, 2, '.', '') }}</p>
                                    </div>
                                </div>

                                <div class="flex flex-col w-full space-y-1 mt-[2rem]">
                                    <div class="flex flex-row space-x-5 m-3">
                                        <span class="h-[2rem] w-[2rem] rounded-full">
                                            <img class="object-cover rounded-full" src="{{ optional($reservation->service)->icon ? asset('storage/' . $reservation->service->icon) : $randomImage }}" alt="icon">
                                        </span>
                                        <span class="text-[14px]">
                                            <p class="font-bold">{{ $reservation->service->service_name ?? $reservation->package->name ?? 'Unknown' }}</p>
                                            <p class="text-gray-500 text-[13px]">P {{ $reservation->service ?  $servicePrice : $packagePrice }}</p>
                                        </span>
                                    </div>
                                    <div class="flex flex-row space-x-5 m-3">
                                        <span class="h-[2rem] w-[2rem] rounded-full">
                                            <img class="object-cover rounded-full" src="{{ asset('storage/' . $reservation->vehicle->vehicleType->icon) }}" alt="icon">
                                        </span>
                                        <span class="text-[14px]">
                                            <p class="font-bold">{{ $reservation->vehicle->vehicleType->name }}</p>
                                            <p class="text-gray-500 text-[13px]">P {{ $vehicleTypePrice }}</p>
                                        </span>
                                    </div>
                                </div>

                                <span class='text-start text-gray-500 text-[12px]'>
                                     <div class="px-4 text-green-400">{{ $reservation->payment->payment_status ?? 'Not yet paid' }}</div>
                                    <div class="px-4">{{ $reservation->schedule->date }}</div>
                                    <div class="px-4">{{ $reservation->schedule->time_slot }}</div>
                                </span>

                                <div class="flex flex-row pt-5 justify-center items-center">
                                    <button 
                                    wire:click="continueReservation({{ $reservation->id }}, '{{ $reservation->service->service_name ?? $reservation->package->name }}')" 
                                        class="px-3 py-1 mt-2 bg-black text-white rounded-full"
                                    wire:loading.attr="disabled"
                                    wire:loading.class="bg-gray-400"
                                    wire:target="continueReservation({{ $reservation->id }}, '{{ $reservation->service->service_name ?? $reservation->package->name }}')"
                                        >
                                            <span wire:loading wire:target="continueReservation({{ $reservation->id }}, '{{ $reservation->service->service_name ?? $reservation->package->name }}')">Processing...</span>
                                        <span class='flex flex-row justify-evenly px-6 items-center' wire:loading.remove wire:target="continueReservation({{ $reservation->id }}, '{{ $reservation->service->service_name ?? $reservation->package->name }}')">
                                            Continue
                                            <i class="fa-solid fa-arrow-right"></i>
                                        </span> 
                                    </button>
                                    <button 
                                            wire:click="confirmCancelReservation({{ $reservation->id }})" 
                                            class="px-3 py-1 bg-red-400 rounded-full text-white" 
                                            wire:loading.attr="disabled"
                                            wire:loading.class="bg-gray-400"
                                        >
                                            <span wire:loading wire:target="confirmCancelReservation({{ $reservation->id }})">...</span>
                                            <span wire:loading.remove wire:target="confirmCancelReservation({{ $reservation->id }})">Cancel</span> 
                                      </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @else
                    <h3 class="text-sm text-gray-500 dark:text-gray-400 text-center">No Reservations</h3>
                    @endif
                </div>


                <div class="overflow-x-auto mt-10 bg-[#262837] m-6 rounded-3xl">
                    @if ($completedReservations->isNotEmpty())
                    <h2 class="text-lg font-medium text-white dark:text-gray-200 mb-4 text-center">Completed Reservations</h2>
                        <table class="w-full text-sm text-left text-gray-300 dark:text-gray-400">
                            <thead class="text-xs text-gray-300 uppercase">
                                <tr>
                                    <th scope="col" class="px-4 py-3">Reservation Id</th>
                                    <th scope="col" class="px-4 py-3">Vehicle</th>
                                    <th scope="col" class="px-4 py-3">Service</th>
                                    <th scope="col" class="px-4 py-3">Package</th>
                                    <th scope="col" class="px-4 py-3">Reservation Status</th>
                                    <th scope="col" class="px-4 py-3">Payment Status</th>
                                    <th scope="col" class="px-4 py-3">Reserved Made</th>
                                    <th scope="col" class="px-4 py-3">Reserved Date</th>
                                    <th scope="col" class="px-4 py-3">Reserved Time</th>
                                    <th scope="col" class="px-4 py-3">Last update</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($completedReservations as $reservation)
                                    <tr class="border-b dark:border-gray-700">
                                        <td class="px-4 py-3">{{ $reservation->id }}</td>
                                        <td class="px-4 py-3">{{ $reservation->vehicle->vehicleType->name ?? 'N/A' }}</td>
                                        <td class="px-4 py-3">{{ $reservation->service->service_name ?? ''}}</td>
                                        <td class="px-4 py-3">{{ $reservation->package->name ?? ''}}</td>
                                        <td class="px-4 py-3 gird place-items-center">
                                            <h1 class="rounded-full w-[100%] bg-green-300 text-green-600 text-center">{{ $reservation->status }}</h1>
                                        </td>
                                        <td class="px-4 py-3">{{ $reservation->payment->payment_status ?? 'N/A' }}</td>
                                        <td class="px-4 py-3">{{ $reservation->reservation_date }}</td>
                                        <td class="px-4 py-3">{{ $reservation->schedule->date }}</td>
                                        <td class="px-4 py-3">{{ $reservation->schedule->time_slot }}</td>
                                        <td class="px-4 py-3">{{ $reservation->updated_at }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <h3 class="text-sm text-gray-500 dark:text-gray-400 text-center">No Completed Reservations</h3>
                    @endif
                </div>


            </div>
        </div>
    </section>
</div>
