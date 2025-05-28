<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Car Wash Reservation') }}</title>

        <!-- Fonts -->
        <!-- <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" /> -->

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-tl from-[#ebf2fd] 0% to-[#b0fa62] 100%">
            <div class="grid grid-cols-1 w-[30%] mt-6 bg-white/75 shadow-lg overflow-hidden sm:rounded-lg ">
                
                <div class='px-6 py-6'>
                    <span>
                    <h1 class="text-[#4393f2] text-2xl text-center font-bold mb-[40px]">
                        Car Wash Reservation
                    </h1>
                    </span>

                    {{ $slot }}
                </div>
            </div>
        </div>
        <x-toaster-hub />
    </body>
</html>
