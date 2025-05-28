

@if(auth()->check() && auth()->user()->role === 'client')
    <button class="{{ $mode === 'dark' ? 'text-white' : 'text-black' }} relative overflow" type="button">
        <i class="fa-regular fa-bell text-[1rem]"></i>
        <span class="top-0 right-0 absolute">
         <h3 class="bg-white w-[0.8rem] h-[0.8rem] text-[0.6rem] text-center rounded-full text-black">{{ $unreadNotificationsCount }}</h3>
        </span>
    </button>
@endif
