<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    /** @use HasFactory<\Database\Factories\PackageFactory> */
    use HasFactory;

    protected $fillable = [
        'name', 
        'description', 
        'discount', 
        'original_price',
        'duration'
    ];

    protected $casts = [
        'original_price' => 'float',
    ];
    

    public function services()
    {
        return $this->belongsToMany(Service::class, 'package_service','package_id', 'service_id')->withTimestamps();
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function getDiscountedPriceAttribute()
    {
        $totalPrice = $this->services->sum('price');
        $discount = $this->discount ?? 0;

        return number_format($totalPrice * (1 - $discount / 100), 2, '.', '');
    }

}
