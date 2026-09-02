<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Document Info
            $table->string('tracking_number')->unique();
            $table->string('document_type'); 
            $table->string('attachment')->nullable(); 
            $table->string('status')->default('pending'); 
            $table->text('remarks')->nullable(); 
            $table->text('admin_remarks')->nullable(); 

            // Claim & Payment Details
            $table->decimal('fee', 8, 2)->default(0.00);
            $table->date('pickup_date')->nullable();
            $table->timestamp('issued_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_requests');
    }
};