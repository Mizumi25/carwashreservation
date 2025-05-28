<?php

use Livewire\Volt\Component;
use App\Models\Reservation;
use App\Models\Vehicle;
use App\Models\Rating;

new class extends Component {
    public $reservationsCount;
    public $vehiclesCount;
    public $reviewsCount;

    public function mount()
    {
        $this->reservationsCount = Reservation::count();
        $this->vehiclesCount = Vehicle::count();
        $this->reviewsCount = Rating::count();
    }

    public function getWavyColor($count)
    {
        return $count > 0 ? 'green' : 'red';
    }
};
?>

<div class="flex px-[30px] space-x-10 items-center flex-row justify-evenly">
    <div class="relative flex flex-col justify-start items-start rounded-[10px] w-[16rem] h-[8rem] font-semibold text-left leading-tight backdrop-blur-lg bg-white/30 border border-white/10 shadow-md p-4">
        <div class="flex items-center">
            <i class="fas fa-calendar-alt text-blue-500 mr-2"></i>
            <span class="text-[17px]">All Reservations</span> 
        </div>
        <div class="text-4xl font-bold mt-2">
            {{ $reservationsCount }}
        </div>
        <div class="relative w-full mt-2"> 
            <div class="h-2 bg-gray-200 rounded-full">
                <div class="h-full bg-{{ $this->getWavyColor($reservationsCount) }}-500 rounded-full" style="width: {{ $reservationsCount }}%;"></div>
            </div>
        </div>
        <div class="flex items-center text-[10px] mt-2"> 
            <i class="fas fa-chart-line{{ $reservationsCount > 0 ? '-up' : '-down' }} text-{{ $reservationsCount > 0 ? 'green' : 'red' }}-500 mr-1"></i>
            <span class="text-{{ $reservationsCount > 0 ? 'green' : 'red' }}-500">{{ abs($reservationsCount) }}%</span>
            <span class="text-gray-500 ml-2">via this week</span>
        </div>
    </div>

    <div class="relative flex flex-col justify-start items-start rounded-[10px] w-[16rem] h-[8rem] font-semibold text-left leading-tight backdrop-blur-lg bg-white/30 border border-white/10 shadow-md p-4">
        <div class="flex items-center">
            <i class="fas fa-car text-green-500 mr-2"></i>
            <span class="text-[17px]">Vehicles</span>
        </div>
        <div class="text-4xl font-bold mt-2">
            {{ $vehiclesCount }}
        </div>
        <div class="relative w-full mt-2"> 
            <div class="h-2 bg-gray-200 rounded-full">
                <div class="h-full bg-{{ $this->getWavyColor($vehiclesCount) }}-500 rounded-full" style="width: {{ $vehiclesCount }}%;"></div>
            </div>
        </div>
        <div class="flex items-center text-[10px] mt-2"> 
            <i class="fas fa-chart-line{{ $vehiclesCount > 0 ? '-up' : '-down' }} text-{{ $vehiclesCount > 0 ? 'green' : 'red' }}-500 mr-1"></i>
            <span class="text-{{ $vehiclesCount > 0 ? 'green' : 'red' }}-500">{{ abs($vehiclesCount) }}%</span>
            <span class="text-gray-500 ml-2">via this week</span>
        </div>
    </div>

    <!-- Reviews Widget -->
    <div class="relative flex flex-col justify-start items-start rounded-[10px] w-[16rem] h-[8rem] font-semibold text-left leading-tight backdrop-blur-lg bg-white/30 border border-white/10 shadow-md p-4">
        <div class="flex items-center">
            <i class="fas fa-star text-red-400 mr-2"></i>
            <span class="text-[17px]">Reviews</span> 
        </div>
        <div class="text-4xl font-bold mt-2">
            {{ $reviewsCount }}
        </div>
        <div class="relative w-full mt-2"> 
            <div class="h-2 bg-gray-200 rounded-full">
                <div class="h-full bg-{{ $this->getWavyColor($reviewsCount) }}-500 rounded-full" style="width: {{ $reviewsCount }}%;"></div>
            </div>
        </div>
        <div class="flex items-center text-[10px] mt-2">
            <i class="fas fa-chart-line{{ $reviewsCount > 0 ? '-up' : '-down' }} text-{{ $reviewsCount > 0 ? 'green' : 'red' }}-500 mr-1"></i>
            <span class="text-{{ $reviewsCount > 0 ? 'green' : 'red' }}-500">{{ abs($reviewsCount) }}%</span>
            <span class="text-gray-500 ml-2">via this week</span>
        </div>
    </div>
</div>