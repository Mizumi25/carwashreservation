<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Car Wash Reservation') }}</title>

    <!-- Fonts -->
    <!-- <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" /> -->
    
    
    <style>
      [x-cloak] {
                display: none !important;
            }
      .car {
        z-index: 2;
      }
    </style>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
</head>
<body class="overflow-x-hidden relative w-[100vw]">
    
    @livewire('notifications')
    @filamentScripts
    @livewireChartsScripts
    
    <div class="grid grid-cols-[auto,1fr] place-items-center gap-0 bg-[#252629]">
        <livewire:layout.sidenavigation />
        <div class="car h-[95vh] w-[83vw] overflow-y-auto rounded-lg {{ $mode === 'dark' ? 'bg-[#262837] text-white' : 'bg-[#f7f7f7] text-black' }} overflow-hidden">
            <livewire:layout.navigation />

            <!-- Page Heading -->
            @if (isset($header))
                <header class="{{ $mode === 'dark' ? 'bg-[#262837]' : 'bg-transparent' }}">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main class="overflow-x-hidden w-[93.5vw]">
                {{ $slot }}
            </main>
        </div>
    </div>
    <x-toaster-hub />
</body>
</html>
