<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmergencyContact extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function residentProfile()
    {
        return $this->belongsTo(ResidentProfile::class, 'resident_profile_id');
    }
}