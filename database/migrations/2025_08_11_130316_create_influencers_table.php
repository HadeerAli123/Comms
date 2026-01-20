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
    Schema::create('influencers', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('ads_link')->nullable();
        $table->string('country')->nullable();
        $table->string('whatsapp_link')->nullable();
        $table->string('instagram_link')->nullable();
        $table->string('tiktok_link')->nullable();
        $table->foreignId('employee_id')->constrained('users')->onDelete('cascade');
        $table->decimal('balance', 10, 2)->default(0);
        $table->text('notes')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('influencers');
    }
};
