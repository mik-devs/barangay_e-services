<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'email',
        'phone_number',
        'password',
        'role',
        'account_status',
        'rejection_reason',
        'profile_photo',
        'signature',
        'last_read_residents',
        'last_read_incidents',
        'last_read_bookings',
        'last_read_documents',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    public function getFullNameAttribute(): string
    {
        $parts = array_filter([
            $this->first_name,
            $this->middle_name,
            $this->last_name,
            $this->suffix,
        ]);

        return implode(' ', $parts);
    }

    // Role helper methods
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isCaptain(): bool
    {
        return $this->role === 'captain' || $this->role === 'punong_barangay';
    }

    public function isKagawad(): bool
    {
        return $this->role === 'kagawad';
    }

    public function isStaff(): bool
    {
        return in_array($this->role, ['staff', 'admin', 'kagawad', 'captain', 'punong_barangay']);
    }

    public function canAccessAdmin(): bool
    {
        return in_array($this->role, ['admin', 'staff', 'kagawad', 'captain', 'punong_barangay']);
    }

    public function isResident(): bool
    {
        return $this->role === 'resident';
    }

    public function profile()
    {
        return $this->hasOne(ResidentProfile::class, 'user_id');
    }
    
    public function residentProfile()
    {
        return $this->profile();
    }
}