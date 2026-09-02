<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class FacilityBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'reference_number',
        'facility_name',
        'booking_date',
        'start_time',
        'end_time',
        'purpose',
        'status',
        'admin_remarks',
        'amount',
    ];

    public function user(): BelongsTo  
    {
        return $this->belongsTo(User::class);
    }

    public function facilityPayment(): HasOne
    {
        return $this->hasOne(FacilityPayment::class);
    }
}