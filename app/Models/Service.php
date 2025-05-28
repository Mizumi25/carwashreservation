<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Reservation;

class Service extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'service_name',
        'icon',
        'description',
        'price',
        'duration',
        'is_active',
        'category_id',
        'popularity',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2', 
            'is_active' => 'boolean', 
            'popularity' => 'integer', 
        ];
    }

    /**
     * Get the status of the service.
     *
     * @return string
     */
    public function status(): string
    {
        return $this->is_active ? 'Active' : 'Inactive';
    }

    /**
     * Increment the popularity counter.
     *
     * @return void
     */
    public function incrementPopularity(): void
    {
        $this->increment('popularity');
    }
    /**
     * Scope a query to only include active services.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    
    
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
    
    public function category()
    {
        return $this->belongsTo(Category::class); 
    }

    public function packages()
    {
        return $this->belongsToMany(Service::class, 'package_service','service_id', 'package_id')->withTimestamps();
    }
    
      
     public function getTotalRevenueAttribute()
    {
        $totalRevenueInDollars = $this->reservations->sum(function ($reservation) {
            return $reservation->payment ? $reservation->payment->amount : 0;
        }) ?: 0;
        return $totalRevenueInDollars * 58.07;
    }
}
