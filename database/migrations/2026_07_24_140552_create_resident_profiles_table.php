<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resident_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('birth_date');
            $table->string('birth_place');
            $table->enum('gender', ['Male', 'Female', 'Other']);
            $table->enum('civil_status', ['Single', 'Married', 'Widowed', 'Separated']);
            $table->string('citizenship')->default('Filipino');
            $table->string('occupation')->nullable();
            $table->string('house_number');
            $table->string('street');
            $table->string('purok_sitio');
            $table->boolean('is_voter')->default(false);
            $table->string('voter_precinct_no')->nullable();
            
            // Verification documents
            $table->string('id_type')->nullable(); // e.g., PhilID, Driver's License
            $table->string('id_number')->nullable();
            $table->string('id_front_path')->nullable();
            $table->string('id_back_path')->nullable();
            $table->string('proof_of_residency_path')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resident_profiles');
    }
};