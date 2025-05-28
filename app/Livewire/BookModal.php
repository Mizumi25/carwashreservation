<?php

namespace App\Livewire;

use Livewire\Component;
use LivewireUI\Modal\ModalComponent;

class BookModal extends ModalComponent
{
    public function render()
    {
        return view('livewire.book-modal');
    }
}
