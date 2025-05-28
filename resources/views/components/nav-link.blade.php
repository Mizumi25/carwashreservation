@props(['active', 'mode'])

@php
$classes = ($active ?? false)
    ? 'relative flex justify-between space-x-2 items-center flex-row items-center px-1 pt-1 border-b-2 
    border-transparent 
    text-[13px] text-blue-500 font-medium leading-5 text-black hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out'
 
    : 'text-gray-600 relative flex justify-between space-x-2 items-center flex-row items-center px-1 pt-1 border-b-2 
    border-transparent 
    text-[13px] font-medium leading-5 text-black hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
     <!-- outline-none rounded-tl-[20px] rounded-bl-[20px] my-[10px] h-[70px] w-[90%] text-center 
      after:absolute after:bg-transparent after:bottom-full after:right-0 after:h-[35px] after:w-[35px]
       after:rounded-br-[18px] before:absolute before:top-full before:right-0 before:h-[35px] before:w-[35px]
        before:rounded-tr-[18px]' after:shadow-[0_20px_0_0_rgb(38,40,55)] before:shadow-[0_-20px_0_0_rgb(38,40,55)]' : 'bg-gray-100 text-black 
     after:shadow-[0_20px_0_0_rgb(243,244,246)] before:shadow-[0_-20px_0_0_rgb(243,244,246)]'-->