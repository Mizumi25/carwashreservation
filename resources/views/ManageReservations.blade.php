
<x-app-layout>
    <x-slot name="header">
        <h2 class="flex px-[30px] items-center rounded-[10px] h-[3rem] font-semibold text-xl text-gray-800 leading-tight {{ $mode === 'dark' ? 'bg-[#313246] text-white' : 'bg-white text-black' }}">
            {{ __('Manage Carwash Reservations') }}
        </h2>
    </x-slot>

    <div class="py-2">
        <div class="w-[93.5vw] mx-auto sm:px-6 lg:px-8 space-y-6">
            <livewire:book.manage-reservations/>
        </div>
    </div>
</x-app-layout>
