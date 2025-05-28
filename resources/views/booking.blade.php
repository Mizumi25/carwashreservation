
<x-app-layout>
    <x-slot name="header">
        <h2 class="flex px-[30px] items-center rounded-[10px] h-[3rem] font-semibold text-xl text-gray-800 leading-tight {{ $mode === 'dark' ? 'bg-[#313246] text-white' : 'bg-white text-black' }} font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add New Carwash Reservation') }}
        </h2>
    </x-slot>

    <div class="py-1">
        <div class="flex justify-start items-center w-full sm:px-6 lg:px-8">
            <livewire:book.reservation-section/>
        </div>
    </div>
</x-app-layout>


