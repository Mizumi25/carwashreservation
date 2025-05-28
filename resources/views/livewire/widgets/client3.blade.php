<?php


use App\Models\Rating;
use Livewire\Volt\Component;

new class extends Component {
    public $ratings;

    public function mount()
{

    $this->ratings = Rating::all();
    }
}; ?>

<div>
@if ($ratings->isNotEmpty())
     <table class="w-full text-sm text-center text-gray-500 dark:text-gray-400">
         <thead class="text-xs text-gray-700 uppercase bg-gray-50">
          <tr>
            <th scope="col" class="px-4 py-3">Service or Services</th>
            <th scope="col" class="px-4 py-3">Rating</th>
            <th scope="col" class="px-4 py-3">Comment</th>
          </tr>
        </thead>
        <tbody>
      @foreach($ratings as $rating)
          <tr class="border-b">
                    <td class="px-4 py-3">{{ $rating->reservation->service->service_name ?? $rating->reservation->package->name }}</td>
                    <td class="px-4 py-3">{{ $rating->rating }}</td>
                    <td class="px-4 py-3">{{ $rating->comment }}</td>
                                    
                </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>
