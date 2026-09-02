<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ResidentProfile extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'resident_profiles'; 

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id', 
        'birth_date', 
        'birth_place', 
        'gender', 
        'civil_status', 
        'citizenship', 
        'occupation', 
        'house_number', 
        'street', 
        'purok_sitio', 
        'is_voter', 
        'voter_precinct_no', 
        'id_type', 
        'id_number', 
        'id_front_path', 
        'id_back_path', 
        'proof_of_residency_path'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'birth_date' => 'date',
        'is_voter'   => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the user account associated with this profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the document requests made by the resident.
     */
    public function documentRequests()
    {
        return $this->hasMany(DocumentRequest::class, 'user_id', 'user_id');
    }

    /**
     * Get the facility bookings made by the resident.
     */
    public function facilityBookings()
    {
        return $this->hasMany(FacilityBooking::class, 'user_id', 'user_id');
    }

    /**
     * Get the incident reports filed by the resident.
     */
    public function incidentReports()
    {
        return $this->hasMany(IncidentReport::class, 'user_id', 'user_id');
    }

    /**
     * Helper Accessor: Buong Address mula sa House No, Street, at Purok/Sitio
     */
    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->house_number ? "House No. {$this->house_number}" : null,
            $this->street ? "{$this->street} St." : null,
            $this->purok_sitio ? "Purok/Sitio {$this->purok_sitio}" : null,
        ]);

        return implode(', ', $parts);
    }

    /**
     * Scope a query to only include verified residents.
     */
    public function scopeVerified($query)
    {
        return $query->where('status', 'Verified');
    }

    /**
     * Scope a query to only include pending residents.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'Pending');
    }
}