<?php

use Livewire\Volt\Component;
use App\Events\MessageSent;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

new class extends Component {
    public bool $showModal = false;
    public string $message = '';
    public array $convo = [];
    public $users = [];
    public ?User $selectedUser = null; // Nullable User object for type clarity

    public function mount()
    {
        // Load messages with user relationship
        $messages = Message::with('user')->get();

        foreach ($messages as $message) {
            if ($message->user) {
                $this->convo[] = [
                    'username' => $message->user->name, // Fetch name of the sender
                    'message' => $message->message,
                ];
            }
        }

        $currentUserRole = Auth::user()->role;

        // Fetch users based on the current user's role
        if ($currentUserRole === 'admin') {
            $this->users = User::where('id', '!=', Auth::id())->get();
        } else {
            $this->users = User::where('role', 'admin')->get();
        }
    }

    public function loadConversation(int $userId)
{
    // Clear the current conversation
    $this->convo = [];

    // Set the selected user
    $this->selectedUser  = User::find($userId);

    if (!$this->selectedUser ) {
        return; // Handle the case where the user is not found
    }

    // Load the conversation
    $this->convo = Message::where(function ($query) use ($userId) {
        $query->where('user_id', Auth::id())
              ->where('receiver_id', $userId);
    })
    ->orWhere(function ($query) use ($userId) {
        $query->where('user_id', $userId)
              ->where('receiver_id', Auth::id());
    })
    ->orderBy('created_at', 'asc')
    ->with('user') // Eager-load user relationship
    ->get()
    ->map(fn($message) => [
        'username' => $message->user->name,
        'message' => $message->message,
    ])
    ->toArray();
}

    public function submitMessage()
{
    if (!$this->selectedUser ) {
        return;
    }

    // Create a new message in the database
    $message = Message::create([
        'user_id' => Auth::id(),
        'receiver_id' => $this->selectedUser ->id,
        'message' => $this->message,
    ]);

    // Dispatch the event using the saved message's data
    MessageSent::dispatch(Auth::id(), Auth::user()->name, $this->message);

    // Update the conversation for the sender
    $this->convo[] = [
        'username' => Auth::user()->name,
        'message' => $this->message,
    ];

    $this->message = ''; 
}

#[On('echo:messages,MessageSent')]
public function listenForMessage(array $data)
{
    if (
        isset($data['username'], $data['message']) &&
        !in_array($data['message'], array_column($this->convo, 'message'))
    ) {
        // Only add the message if it's from the current user
        if ($data['username'] !== Auth::user()->name) {
            // Check if the message is for the currently selected user
            if ($this->selectedUser  && $data['username'] === $this->selectedUser ->name) {
                $this->convo[] = [
                    'username' => $data['username'],
                    'message' => $data['message'],
                ];
            }
        }
    }
}

    

    public function toggleModal()
    {
        $this->showModal = !$this->showModal;
    }

    public function backToUserList()
    {
        $this->selectedUser = null;
    }
};
?>

<div x-data="{ showModal: false }">
    <button
        class="text-black rounded-full text-[15px] flex items-center justify-center"
        @click="showModal = true"
    >
        <i class="fa-regular fa-comment"></i>
    </button>

    
    <template x-if="showModal">
        <div class="fixed right-0 bottom-1 z-[999] flex items-center justify-center overflow-hidden">

            <div class="fixed inset-0 bg-black opacity-50" @click="showModal = false"></div>


            <div class="bg-white h-[90vh] w-[32vw] rounded-lg shadow-lg p-4 relative">
                <button
                    class="absolute top-2 right-2 text-gray-600 hover:text-gray-800"
                    @click="showModal = false"
                >
                    <i class="fa fa-times"></i>
                </button>

                <div class="flex flex-col h-full">
                    @if (!$selectedUser )
                        <p class="text-lg font-semibold">Select a User</p>
                        <ul class="mt-4 overflow-x-hidden overflow-y-auto">
                            @foreach ($users as $user)
                                <li>
                                    <button 
                                        class="text-left w-full p-2 hover:bg-gray-200 rounded flex flex-row space-x-2 items-center"
                                        wire:click="loadConversation({{ $user->id }})"
                                    >
                                        <div class="relative">
                                            <img src="{{ asset('storage/' . $user->profile_picture) }}" 
                                                 alt="Profile Picture" 
                                                 class="h-[2rem] w-[2rem] cursor-pointer object-cover rounded-full">
                                            @if ($user->is_active)
                                                <span 
                                                    class="absolute bottom-0 right-0 h-[1rem] w-[1rem] bg-green-500 rounded-full border border-white">
                                                </span>
                                            @endif
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="font-semibold">{{ $user->name }}</span>
                                            @if (!$user->is_active)
                                                <span class="text-gray-500 text-sm">
                                                    {{ $user->updated_at->diffForHumans() }}
                                                </span>
                                            @endif
                                        </div>
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <button 
                            class="text-black rounded p-2 mb-4 absolute left-0"
                            wire:click="backToUserList"
                        >
                            <i class="fa-solid fa-chevron-left"></i>
                        </button>

                        <div class="flex-1 overflow-y-auto pt-11">
                            @foreach ($convo as $convoItem)
                                <div class="flex items-start mb-2 {{ $convoItem['username'] === Auth::user()->name ? 'justify-end' : '' }}">
                                    @if ($convoItem['username'] !== Auth::user()->name)
                                        <div class="flex items-start">
                                            @if ($user = User::where('name', $convoItem['username'])->first())
                                                @if ($user->profile_picture)
                                                    <img src="{{ asset('storage/' . $user->profile_picture) }}" 
                                                         alt="Profile Picture" 
                                                         class="h-[2rem] w-[2rem] object-cover rounded-full mr-2">
                                                @endif
                                            @endif
                                            <div>
                                                <span class="text-sm font-semibold text-gray-600">{{ $convoItem['username'] }}</span>
                                                <div class="bg-gray-200 p-2 rounded-lg max-w-xs">
                                                    {{ $convoItem['message'] }}
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="flex items-start">
                                            <div class="bg-blue-500 text-white p-2 rounded-lg max-w-xs ml-auto">
                                                {{ $convoItem['message'] }}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>

             
                        <form class="w-full mt-auto" wire:submit.prevent="submitMessage">
                            <div class="flex flex-row gap-2">
                                <x-text-input
                                    type="text"
                                    class="w-full border rounded px-2 py-1"
                                    placeholder="Type a message ..."
                                    wire:model="message"
                                />
                                <button type="submit" class="bg-blue-500 text-white px-4 py-1 rounded">
                                    <i class="fa-solid fa-paper-plane"></i>
                                </button> </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </template>
</div>
