
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facility_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_booking_id')->constrained()->onDelete('cascade');
            $table->foreignId('resident_id')->constrained('users')->onDelete('cascade');
            $table->string('reference_number')->unique();
            $table->decimal('amount', 10, 2);
            $table->string('payment_method')->nullable(); // gcash, maya, cash, etc.
            $table->string('payment_status')->default('pending'); // pending, paid, cancelled
            $table->timestamp('paid_at')->nullable();
            $table->string('or_number')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facility_payments');
    }
};