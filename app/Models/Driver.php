<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Driver extends Model
{
    protected $fillable = [
        'name',
        'phone_number',
        'license_number',
        'vehicle_number',
        'status'
    ];

    protected $casts = [
        'status' => 'string'
    ];

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}
