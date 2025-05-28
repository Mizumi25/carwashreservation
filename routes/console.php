<?php

use Illuminate\Foundation\Inspiring;
use App\Console\Commands\UpdateReservationStatus; 
use App\Console\Commands\SendReservationNotice; 
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();


Artisan::command('reservations:update-status', function () {
    $this->call(UpdateReservationStatus::class);
})->everyMinute();


Artisan::command('reservations:send-reservation-notice', function () {
    $this->call(SendReservationNotice::class);
})->everyMinute();