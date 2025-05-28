<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;



new class extends Component
{
    use WithFileUploads;

    public $profile_picture;
    
    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->profile_picture = Auth::user()->profile_picture;
    }

    /**
     * Update the profile information and handle profile picture upload.
     */
    public function updateProfilePicture(): void
    {
        $user = Auth::user();
    
        $validated = $this->validate([
            'profile_picture' => 'image',
        ]);
        $user->fill($validated);
    
        if ($this->profile_picture) {
            $filePath = $this->profile_picture->store('profile_pictures', 'public');
            $user->profile_picture = $filePath;
        }
        $user->save();
        $this->dispatch('profile-picture-updated', name: $user->name);
    }


    /**
     * Send an email verification notification to the current user.
     */
    
};
?>

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Picture') }}
        </h2>

        <p class="mt-1 text-sm text-gray-800">
            {{ __("Update your account's profile picture.") }}
        </p>
    </header>

    <form wire:submit="updateProfilePicture" class="mt-6 space-y-6 grid grid-cols-2 w-[100%] place-items-center relative">
    
        <!-- Profile Picture -->
        <div>
            
            <div class="h-full w-full grid place-items-center">
                @if(Auth::user()->profile_picture)
                    <!-- Image Preview -->
                    <img id="profileImagePreview"  src="{{ asset('storage/' . Auth::user()->profile_picture) }}" alt="Profile Picture" class="h-[12rem] w-[12rem] cursor-pointer object-cover rounded-full backdrop-filter backdrop-grayscale backdrop-blur-md backdrop-contrast-200 border-8 border-white" onclick="document.getElementById('fileInput').click();">
                @endif
                <livewire:greendot />

                <!-- Hidden File Input -->
                <input type="file" wire:model="profile_picture" name="profile_picture" id="fileInput" class="hidden" accept="image/*" onchange="previewImage(event)">
            </div>
        </div>

      

        <div class="flex items-center gap-4 absolute bottom-3 right-10">
            <x-primary-button wire:loading.attr="disabled"
                wire:loading.class="bg-gray-400"
                wire:target="updateProfilePicture"
                >
                <span wire:loading wire:target="updateProfilePicture">Saving...</span>
                <span wire:loading.remove wire:target="updateProfilePicture">{{ __('Save') }}</span></x-primary-button>

            <x-action-message class="me-3" on="profile-picture-updated">
                {{ __('Saved.') }}
            </x-action-message>
        </div>

        
    </form>
    <script>
    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function(){
            const output = document.getElementById('profileImagePreview');
            output.src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    }
</script>
</section>


