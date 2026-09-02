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
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('last_read_residents')->nullable();
            $table->timestamp('last_read_incidents')->nullable();
            $table->timestamp('last_read_bookings')->nullable();
            $table->timestamp('last_read_documents')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'last_read_residents',
                'last_read_incidents',
                'last_read_bookings',
                'last_read_documents',
            ]);
        });
    }
};