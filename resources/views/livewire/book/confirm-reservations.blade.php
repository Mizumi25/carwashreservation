<?php

use Livewire\Volt\Component;
use LaravelDaily\Invoices\Invoice;
use LaravelDaily\Invoices\Classes\Buyer;
use LaravelDaily\Invoices\Classes\InvoiceItem;
use App\Models\Reservation; // Make sure to import the Reservation model

new class extends Component {
    public $pdfUrl;
    public $reservationId;

    public function mount($reservationId)
    {
        $this->reservationId = $reservationId;

        $reservation = Reservation::with(['service', 'vehicle.vehicleType'])->findOrFail($reservationId);

        $customer = new Buyer([
            'name' => $reservation->user->name,
            'custom_fields' => [
                'email' => $reservation->user->email, 
            ],
        ]);

        $item = InvoiceItem::make($reservation->service->service_name ?? $reservation->package->name) 
            ->pricePerUnit($reservation->service->price ?? ($reservation->package->original_price) * ($reservation->package->discount) / 100); 

        $invoice = Invoice::make()
            ->buyer($customer)
            ->addItem($item)
            ->currencySymbol('$')
            ->filename('Invoice_' . $customer->name)
            ->save('public');

        $this->pdfUrl = asset('storage/Invoice_' . $customer->name . '.pdf');
    }

   
};?>


 <div class="grid grid-cols-2 place-items-center mx-2 p-4 sm:p-8 shadow sm:rounded-lg px-[30px] {{ $mode === 'dark' ? 'bg-[#313246] text-white' : 'bg-white text-black' }} overflow-hidden shadow-sm sm:rounded-lg w-[91%] rounded-[10px]">
    <ol class="relative text-gray-500 border-s border-gray-200 dark:border-gray-700 dark:text-gray-400">                  
        <li class="mb-10 ms-6">            
            <span class="absolute flex items-center justify-center w-8 h-8 bg-gray-100 rounded-full -start-4 ring-4 ring-white dark:ring-gray-900 dark:bg-gray-700">
                4/5
            </span>
            <h3 class="font-medium leading-tight">Personal Info</h3>
            <p class="text-sm">Currently Pending</p>
        </li>
        <li class="mb-10 ms-6">
            <span class="absolute flex items-center justify-center w-8 h-8 bg-gray-100 rounded-full -start-4 ring-4 ring-white dark:ring-gray-900 dark:bg-gray-700">
                5/5
            </span>
            <h3 class="font-medium leading-tight">Account Info</h3>
            <p class="text-sm">Pay total amount here</p>
        </li>
        <li class="mb-10 ms-6">
            <span class="absolute flex items-center justify-center w-8 h-8 bg-green-100  rounded-full -start-4 ring-4 ring-white dark:ring-gray-900 dark:bg-blue-900">
               C
            </span>
            <h3 class="font-medium leading-tight">Review</h3>
            <p class="text-sm">Step details here</p>
        </li>
    </ol> 
    <div>
    <div>
        @if($pdfUrl)
            <iframe src="{{ $pdfUrl }}" width="100%" height="600px"></iframe>
        @else
            <p>Loading invoice...</p>
        @endif
    </div>
</div>
  </div>