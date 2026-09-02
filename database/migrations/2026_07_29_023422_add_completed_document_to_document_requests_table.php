<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('document_requests', function (Blueprint $table) {
        $table->string('completed_document')->nullable()->after('attachment');
    });
}

public function down()
{
    Schema::table('document_requests', function (Blueprint $table) {
        $table->dropColumn('completed_document');
    });
}
    
};
