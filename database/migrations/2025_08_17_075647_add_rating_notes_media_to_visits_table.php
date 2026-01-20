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
        Schema::table('visits', function (Blueprint $table) {
            $table->unsignedTinyInteger('rating')->nullable()->after('is_announced');
            $table->text('accept_notes')->nullable()->after('rating');
            $table->string('media')->nullable()->after('accept_notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            $table->dropColumn(['rating', 'accept_notes', 'media']);
        });
    }
};