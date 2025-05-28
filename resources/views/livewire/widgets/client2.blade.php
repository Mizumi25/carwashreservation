<?php


use App\Models\Reservation;
use Livewire\Volt\Component;

new class extends Component {
    public $reservations;

    public function mount()
    {
        $this->reservations = Reservation::where('user_id', auth()->id())->whereNotIn('status', ['cancelled', 'decline', 'completed'])  ->get();
    }
}; ?>

<div>
@if ($reservations->isNotEmpty())
     <table class="w-full text-sm text-center text-gray-500 dark:text-gray-400">
         <thead class="text-xs text-gray-700 uppercase bg-gray-50">
          <tr>
            <th scope="col" class="px-4 py-3">Reservation Id</th>
            <th scope="col" class="px-4 py-3">Vehicle</th>
            <th scope="col" class="px-4 py-3">Service</th>
            <th scope="col" class="px-4 py-3">Package</th>
            <th scope="col" class="px-4 py-3">Reservation Status</th>
          </tr>
        </thead>
        <tbody>
      @foreach($reservations as $reservation)
          <tr class="border-b">
                    <td class="px-4 py-3">{{ $reservation->id }}</td>
                    <th scope="row" class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        {{ $reservation->vehicle->vehicleType->name ?? 'N/A' }} 
                    </th>
                    <td class="px-4 py-3">{{ $reservation->service->service_name ?? '' }}</td>
                    <td class="px-4 py-3">{{ $reservation->package->name ?? '' }}</td>
                    <td class="px-4 py-3 gird place-items-center">
                        <h1 class="bg-blue-300 text-blue-600 rounded-full w-[100%]">{{ $reservation->status }}</h1>
                    </td>
                                    
                </tr>
            @endforeach
        </tbody>
    </table>
    @else
        <div class="flex flex-col justify-center items-center">
            <i class="fas fa-times text-slate-300 text-4xl"></i> 
            <span class="mt-2 text-slate-400">No Reservations</span> 
        </div>
    @endif
</div>
