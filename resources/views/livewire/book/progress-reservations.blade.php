  <?php
  
  use Livewire\Volt\Component;
  
  new class extends Component {
    public $reservationId;
    
    public function mount($reservationId)
    {
        $this->reservationId = $reservationId;

    }

  }; ?>
  
  <div class="grid grid-cols-1 h-[80vh] place-items-center mx-2 p-4 sm:p-8 shadow sm:rounded-lg px-[30px] {{ $mode === 'dark' ? 'bg-[#313246] text-white' : 'bg-white text-black' }} overflow-hidden shadow-sm sm:rounded-lg w-[85%] rounded-[10px]">
      Reservation ID: {{ $reservationId }}
      Working In Progress...
      <div class="absolute right-[47%] transform translate-x-[-50%] top-[17%]">
          <div class="p-4 bg-gradient-to-tr animate-spin from-green-500 to-blue-500 via-purple-500 rounded-full">
              <div class="bg-white rounded-full">
                  <div class="w-24 h-24 rounded-full"></div>
              </div>
          </div>
      </div>
  </div>
