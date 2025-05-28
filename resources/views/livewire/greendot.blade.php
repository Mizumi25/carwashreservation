<?php

use Livewire\Volt\Component;
use App\Events\UserActivityUpdated;

new class extends Component {
    public $is_active = false;

    protected $listeners = ['userStatusUpdated' => 'updateStatus'];

    public function mount()
    {
        
        if (auth()->check()) {
            $this->is_active = auth()->user()->is_active;
        }
    }

    public function updateStatus($status)
    {
        $this->is_active = $status;
    }
}; ?>

<div class="bg-red-700 relative h-full w-full">
    @if ($is_active)
        <span class="bg-green-500 w-[2rem] h-[2rem] rounded-full absolute bottom-0 right-[30%] z-[10]"></span> <!-- Active -->
    @else
        <span class="bg-gray-500 w-[2rem] h-[2rem] rounded-full absolute bottom-0 right-[30%] z-[10]"></span> <!-- Inactive -->
    @endif
</div>



