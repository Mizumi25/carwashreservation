<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Broadcast;
use App\Events\UserActivityUpdated;

class UserActivityUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user_id;
    public $is_active;

    public function __construct($user_id, $is_active)
    {
        $this->user_id = $user_id;
        $this->is_active = $is_active;

        // Optionally update the database (update user's 'is_active' column)
        User::where('id', $user_id)->update(['is_active' => $is_active]);
    }

    public function broadcastAs()
    {
        function setBroadcastDriver($driver)
        {
            config(['broadcasting.default' => $driver]);
        }
        setBroadcastDriver('');

        return 'user-activity-updated';
    }

    public function broadcastOn()
    {
        return new Channel('userStatus' . $this->user_id); 
    }

    public function broadcastWith()
    {
        return ['status' => $this->is_active]; 
    }
}
