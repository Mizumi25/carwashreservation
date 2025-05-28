<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'receiver_id',
        'message',
    ];

    // Define relationship to User (Sender)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Define relationship to Receiver (Optional)
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}



