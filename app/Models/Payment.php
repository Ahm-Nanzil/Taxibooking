<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'booking_id',
        'amount',
        'transaction_id',
        'status',
        'payment_method',
        'payment_details'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'status' => 'string'
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
