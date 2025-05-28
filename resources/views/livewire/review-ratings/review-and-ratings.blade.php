<?php

use Livewire\Volt\Component;
use App\Models\Rating;
use App\Models\Reservation;

new class extends Component {
    public $reservationId;
    public $rating;
    public $comment;
    public $currentId;
    public $reservation;
    public $hideForm;

    protected $rules = [
        'rating' => ['required', 'in:1,2,3,4,5'],
        'comment' => 'required',
    ];

    public function mount($reservationId)
    {
        $this->reservationId = $reservationId;

        if (auth()->user()) {
            $existingRating = Rating::where('user_id', auth()->user()->id)
                ->where('reservation_id', $this->reservationId)
                ->first();

            if ($existingRating) {
                $this->rating = $existingRating->rating;
                $this->comment = $existingRating->comment;
                $this->currentId = $existingRating->id;
            }
        }
    }

    public function rate()
    {
        $this->validate();

        $existingRating = Rating::where('user_id', auth()->user()->id)
            ->where('reservation_id', $this->reservationId)
            ->first();

        if ($existingRating) {
            $existingRating->update([
                'rating' => $this->rating,
                'comment' => $this->comment,
                'status' => 1,
            ]);
            session()->flash('message', 'Your rating has been updated!');
        } else {
            Rating::create([
                'user_id' => auth()->user()->id,
                'reservation_id' => $this->reservationId,
                'rating' => $this->rating,
                'comment' => $this->comment,
                'status' => 1,
            ]);
            session()->flash('message', 'Thank you for your rating!');
        }

        $reservation = Reservation::find($this->reservationId);
        if ($reservation) {
            $reservation->status = 'completed';
            $reservation->save();
        }
        return redirect()->route('reservations.manage');
    }
};; ?>

<div>
    <h3>Review for Reservation ID: {{ $reservationId }}</h3>

    @if (session()->has('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif

    @if (!$hideForm)
        <form wire:submit.prevent="rate">
            <div x-data="{
                rating: @entangle('rating'),
                setRating(value) {
                    this.rating = value;
                }
            }" class="flex items-center space-x-1">
                <template x-for="star in [1, 2, 3, 4, 5]" :key="star">
                    <svg
                        x-bind:class="{'text-yellow-500': rating >= star, 'text-gray-300': rating < star}"
                        x-on:click="setRating(star)"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="currentColor"
                        class="w-8 h-8 cursor-pointer"
                        viewBox="0 0 20 20"
                    >
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.18 3.63a1 1 0 00.95.69h3.81c.969 0 1.371 1.24.588 1.81l-3.08 2.24a1 1 0 00-.364 1.118l1.18 3.63c.3.921-.755 1.688-1.54 1.118l-3.08-2.24a1 1 0 00-1.175 0l-3.08 2.24c-.784.57-1.838-.197-1.539-1.118l1.18-3.63a1 1 0 00-.364-1.118l-3.08-2.24c-.784-.57-.38-1.81.588-1.81h3.81a1 1 0 00.95-.69l1.18-3.63z"/>
                    </svg>
                </template>
            </div>
            @error('rating') <span class="text-red-500">{{ $message }}</span> @enderror

            <div class="mt-4">
                <label for="comment">Comment:</label>
                <textarea wire:model="comment" id="comment" class="w-full bg-gray-100 rounded border border-gray-400 leading-normal resize-none h-20 py-2 px-3 font-medium placeholder-gray-700 focus:outline-none focus:bg-white"></textarea>
                @error('comment') <span class="text-red-500">{{ $message }}</span> @enderror
            </div>
            <button type="submit" class="mt-4 px-4 py-2 rounded-md text-white bg-green-500">Submit Rating</button>
        </form>
    @endif
</div>
