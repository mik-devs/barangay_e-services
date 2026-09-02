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
    Schema::table('resident_profiles', function (Blueprint $table) {
        $table->string('emergency_contact_name')->nullable();
        $table->string('emergency_contact_number')->nullable();
        $table->string('emergency_contact_relation')->nullable();
    });
}

public function down(): void
{
    Schema::table('resident_profiles', function (Blueprint $table) {
        $table->dropColumn([
            'emergency_contact_name',
            'emergency_contact_number',
            'emergency_contact_relation',
        ]);
    });
}
};
