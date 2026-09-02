<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\ResidentProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin Account
        User::create([
            'first_name' => 'Barangay',
            'last_name' => 'Admin',
            'email' => 'admin@barangay.gov.ph',
            'phone_number' => '09170000001',
            'password' => Hash::make('Admin12345!'),
            'role' => 'admin',
            'account_status' => 'verified',
            'email_verified_at' => now(),
        ]);

        // Staff Account
        User::create([
            'first_name' => 'Maria',
            'last_name' => 'Santos',
            'email' => 'staff@barangay.gov.ph',
            'phone_number' => '09170000002',
            'password' => Hash::make('Staff12345!'),
            'role' => 'staff',
            'account_status' => 'verified',
            'email_verified_at' => now(),
        ]);

        // Sample Verified Resident Account
        $resident = User::create([
            'first_name' => 'Juan',
            'middle_name' => 'Dela',
            'last_name' => 'Cruz',
            'email' => 'juan@gmail.com',
            'phone_number' => '09171234567',
            'password' => Hash::make('Resident12345!'),
            'role' => 'resident',
            'account_status' => 'verified',
            'email_verified_at' => now(),
        ]);

        ResidentProfile::create([
            'user_id' => $resident->id,
            'birth_date' => '1995-06-15',
            'birth_place' => 'Manila',
            'gender' => 'Male',
            'civil_status' => 'Single',
            'citizenship' => 'Filipino',
            'occupation' => 'Software Developer',
            'house_number' => '123',
            'street' => 'Rizal Street',
            'purok_sitio' => 'Purok 3',
            'is_voter' => true,
            'voter_precinct_no' => '0012A',
        ]);
    }
}