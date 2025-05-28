<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Client Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="w-[90%] sm:px-[100px] lg:px-[100px] space-y-6 grid grid-rows-2 gap-x-1 items-start place-items-start">
            <div class="{{ $mode === 'dark' ? 'bg-[#313246] text-white' : 'bg-white text-black' }} overflow-hidden 
            shadow-sm sm:rounded-lg w-[100%] rounded-[50px] relative">
                <div class="w-full">
                    <div class="absolute h-[11.5rem] w-full bg-gradient-to-tl from-[#ebf2fd] 0% to-[#e6fcce] 100%"></div>
                    <livewire:profile.update-profile-picture />
                    <livewire:profile.update-profile-information-form />
                </div>
            </div>

            <div class="grid grid-cols-1 gap-y-5">
                <div class="p-4 sm:p-8 px-[30px] {{ $mode === 'dark' ? 'bg-[#313246] text-white' : 'bg-white text-black' }} overflow-hidden shadow-sm sm:rounded-lg w-[91%] rounded-[10px]">
                    <div class="max-w-xl">
                        <livewire:profile.update-password-form />
                    </div>
                </div>

                <div class="p-4 sm:p-8 px-[30px] {{ $mode === 'dark' ? 'bg-[#313246] text-white' : 'bg-white text-black' }} overflow-hidden shadow-sm sm:rounded-lg w-[91%] rounded-[10px]">
                    <div class="max-w-xl">
                        <livewire:profile.delete-user-form />
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
