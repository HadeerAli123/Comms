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
        Schema::create('commissions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('marketer_id')->constrained()->onDelete('cascade');
    $table->foreignId('site_id')->constrained()->onDelete('cascade');
    $table->integer('visitors')->default(0);
    $table->integer('dishes')->default(0);
    $table->decimal('amount', 10, 2);
    $table->enum('type', ['credit','debit'])->default('credit');
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commissions');
    }
};