<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncidentReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'report_number',
        'incident_type',
        'location',
        'incident_date',
        'description',
        'attachment',
        'status',
        'admin_remarks',
    ];

    protected $casts = [
        'incident_date' => 'datetime',
    ];

    /**
     * Relationship: Incident Report belongs to a User/Resident
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}