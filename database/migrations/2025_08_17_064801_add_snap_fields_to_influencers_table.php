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
    Schema::table('influencers', function (Blueprint $table) {
        $table->string('snap')->nullable()->after('country_id');
        $table->string('snap_link')->nullable()->after('snap');
        $table->string('phone')->nullable()->after('snap_link');
        $table->string('pdf')->nullable()->after('phone');
    });
}

public function down(): void
{
    Schema::table('influencers', function (Blueprint $table) {
        $table->dropColumn(['snap', 'snap_link', 'phone', 'pdf']);
    });
}
};