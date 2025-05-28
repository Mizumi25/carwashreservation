<?php

use Livewire\Volt\Component;

new class extends Component {
    public $currentTime;
    public $currentDate;

    public function mount()
    {
        $this->updateTime();
    }

    public function updateTime()
    {
        $this->currentTime = now()->format('H:i:s'); 
        $this->currentDate = now()->format('l, F j, Y'); 
    }
}; ?>

<div x-data="{
        time: @entangle('currentTime').defer,
        date: @entangle('currentDate').defer,
        updateTime() {
            setInterval(() => {
                this.time = new Date().toLocaleTimeString('en-GB', { hour12: false });
                this.date = new Date().toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
            }, 1000);
        }
    }" x-init="updateTime()"
    class="flex flex-row w-[250px] items-center justify-center p-4 border absolute top-3 left-[40%] rounded-lg shadow-lg bg-white/60 z-50 space-x-7">
    
    <h1 class="text-[15px] font-bold" x-text="time"></h1>
    <p class="text-[10px] text-gray-600 mt-2" x-text="date"></p>
</div>
