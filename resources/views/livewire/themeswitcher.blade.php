<?php

use Livewire\Volt\Component;

new class extends Component {
    public $mode;

    protected $listeners = ['ModeView'];

    public function mount()
    {
        $this->mode = session('theme', 'light');
    }

    public function ModeView($Color)
    {

        $this->mode = $Color;
    }

    public function SwitchMode($SwitcMode)
    {
      
        $this->mode = $SwitcMode;
        session(['theme' => $SwitcMode]);

        $this->dispatch('theme-updated', ['mode' => $SwitcMode]);
    }
};

?>

<div x-data="{ darkMode: @entangle('mode').defer === 'dark' }" 
     x-bind:class="{ 'dark': darkMode }"
     x-init="
        darkMode = localStorage.getItem('theme') === 'dark' || (localStorage.getItem('theme') === null && window.matchMedia('(prefers-color-scheme: dark)').matches);
        $watch('darkMode', value => {
            localStorage.setItem('theme', value ? 'dark' : 'light');
            $wire.SwitchMode(value ? 'dark' : 'light');
        });
     ">

    <div class="fi-theme-switcher grid grid-flow-col place-items-center gap-x-1">
        <!-- Dark Theme Button -->
        <button
            aria-label="Dark Theme"
            type="button"
            x-on:click="
                darkMode = true;
                localStorage.setItem('theme', 'dark');
                $wire.SwitchMode('dark');
                setTimeout(() => window.location.reload(), 1000); 
            "
            :class="{
                'bg-blue-500 text-white': darkMode,        /* Selected: Blue background, white text */
                'bg-transparent text-black': !darkMode     /* Unselected: Transparent background, black text */
            }"
            class="fi-theme-switcher-btn px-4 py-2 rounded-md"
        >
            <x-filament::icon alias="panels::theme-switcher.dark-button" icon="heroicon-m-moon" class="h-5 w-5" />
        </button>

        <!-- System Theme Button -->
        <button
            aria-label="System Theme"
            type="button"
            x-on:click="
                darkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;
                localStorage.removeItem('theme'); 
                $wire.SwitchMode('system'); 
                setTimeout(() => window.location.reload(), 1000); 
            "
            :class="{
                'bg-blue-500 text-white': !darkMode && $wire.mode === 'light',   /* Selected: Blue background, white text */
                'bg-transparent text-black': !(darkMode || $wire.mode === 'light') /* Unselected: Transparent background, black text */
            }"
            class="fi-theme-switcher-btn px-4 py-2 rounded-md"
        >
            <x-filament::icon alias="panels::theme-switcher.light-button" icon="heroicon-m-sun" class="h-5 w-5" />
        </button>
    </div>
    <script>
  document.addEventListener('DOMContentLoaded', () => {
    const initialMode = localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
    document.documentElement.classList.toggle('dark', initialMode === 'dark');

    window.addEventListener('theme-updated', (event) => {
      const newMode = event.detail.mode;
      document.documentElement.classList.toggle('dark', newMode === 'dark');
      localStorage.setItem('theme', newMode);
    });

    Livewire.on('view-mode', newMode => {
      document.documentElement.classList.toggle('dark', newMode === 'dark');
    });
  });
</script>
</div>


