<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('incident_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('report_number')->unique(); // Unique Tracking/Report Code
            $table->string('incident_type');          // Noise, Accident, Theft, etc.
            $table->string('location');               // Incident location
            $table->dateTime('incident_date');         // Date and time occurred
            $table->text('description');             // Incident details
            $table->string('attachment')->nullable();  // Photo / proof attachment path
            $table->string('status')->default('pending'); // pending, investigating, resolved, dismissed
            $table->text('admin_remarks')->nullable(); // Response / notes from barangay official
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incident_reports');
    }
};