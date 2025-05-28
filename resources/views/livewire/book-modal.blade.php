<?php

use Livewire\Volt\Component;
use Illuminate\Support\Facades\Auth;
use LivewireUI\Modal\ModalComponent;

new class extends ModalComponent {
  public static function modalMaxWidth(): string
  {
      return '7xl';
  }
}; 
?>

<div class="bg-white z-[99999] rounded-[13px] overflow-x-hidden p-10 flex justify-center items-center text-center flex-col overflow-y-auto">
    <h1>You must be authenticated to make a reservation</h1>
    <livewire:pages.auth.login />
</div>
