<?php

namespace App\Events;

use App\Models\Reservation;
use App\Models\User;
use App\Models\Service;
use App\Models\Package; 
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReservationCreate implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public $reservation;
    public $user;
    public $eventServiceOrPackage; 

    public function __construct(Reservation $reservation, User $user, $eventServiceOrPackage)
    {
        $this->reservation = $reservation;
        $this->user = $user;
        $this->eventServiceOrPackage = $eventServiceOrPackage; 
    }

    public function broadcastOn()
    {
        return new PrivateChannel('admin-notifications1');
    }
    
    public function broadcastWith()
    {
        return [
            'reservation_id' => $this->reservation->id,
            'user_name' => $this->user->name,
            'service_name' => $this->eventServiceOrPackage instanceof Service ? $this->eventServiceOrPackage->service_name : null, 
            'package_name' => $this->eventServiceOrPackage instanceof Package ? $this->eventServiceOrPackage->name : null,
            'message' => 'A new reservation has been made.',
        ];
    }
}