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
       Schema::create('marketer_site', function (Blueprint $table) {
    $table->id();
    $table->foreignId('marketer_id')->constrained()->onDelete('cascade');
    $table->foreignId('site_id')->constrained()->onDelete('cascade');
    $table->decimal('commission_rate', 5, 2)->default(0);
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketer_site');
    }
};