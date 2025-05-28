<x-app-layout>
    <x-slot name="header">
        <livewire:widgets.stats />
    </x-slot>

    <div class="py-0">
        <div class="flex flex-row justify-center items-start w-[85%] sm:px-6 lg:px-8 space-x-0">
            <div class="flex flex-col space-y-[1rem] w-[100%] h-[55rem] px-[30px]">
                <div class="{{ $mode === 'dark' ? 'bg-[#313246] text-white' : 'bg-white text-black' }} 
                overflow-hidden shadow-sm sm:rounded-lg w-full rounded-[10px] h-[60%]">
                    <livewire:widgets.client1 />
                </div>
                <div class="{{ $mode === 'dark' ? 'bg-[#313246] text-white' : 'bg-white text-black' }} 
                overflow-auto shadow-sm sm:rounded-lg w-full rounded-[10px] h-[40%]">
                    <livewire:widgets.client2 />
                </div>
            </div>

            <div class="flex flex-col space-y-[1rem] w-[80%] h-[25rem] px-[30px]">
                <div class="relative {{ $mode === 'dark' ? 'bg-[#313246] text-white' : 'bg-white text-black' }} 
                overflow-hidden shadow-sm sm:rounded-lg w-full rounded-[10px] h-[60%]">
                    <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}" alt="Profile Picture" class="inset-0 
                    cursor-pointer object-cover backdrop-filter backdrop-grayscale backdrop-blur-md backdrop-contrast-900">">
                    <div class='absolute bottom-0 bg-gray-800 opacity-40 left-0 w-full h-full'></div>
                    <div class='absolute left-[15%] bottom-[20%] text-white'>
                        <h1>{{ Auth::user()->name }}</h1>
                        <p>{{ Auth::user()->email }}</p>
                    </div>
                </div>
                <div class="{{ $mode === 'dark' ? 'bg-[#313246] text-white' : 'bg-white text-black' }} 
                overflow-hidden shadow-sm sm:rounded-lg w-full rounded-[10px] h-[40%]">
                    <livewire:widgets.client3 />
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
