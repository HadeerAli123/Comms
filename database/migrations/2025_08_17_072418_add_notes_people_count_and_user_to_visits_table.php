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
            $table->text('notes')->nullable()->after('is_announced');
            $table->unsignedInteger('people_count')->default(0)->after('notes');
            $table->unsignedBigInteger('user_id')->nullable()->after('people_count');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['notes', 'people_count', 'user_id']);
        });
    }
};