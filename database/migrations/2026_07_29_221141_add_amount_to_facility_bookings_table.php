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
    Schema::table('facility_bookings', function (Blueprint $table) {
        $table->decimal('amount', 10, 2)->nullable()->after('status');
    });
}

public function down(): void
{
    Schema::table('facility_bookings', function (Blueprint $table) {
        $table->dropColumn('amount');
    });

    }
};
