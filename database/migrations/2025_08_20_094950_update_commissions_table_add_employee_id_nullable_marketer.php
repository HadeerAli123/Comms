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
        Schema::table('commissions', function (Blueprint $table) {
            // خلي marketer_id يقبل null
            $table->unsignedBigInteger('marketer_id')->nullable()->change();

            // اضف employee_id
            $table->unsignedBigInteger('employee_id')->nullable()->after('marketer_id');

            // علاقات
            $table->foreign('employee_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('commissions', function (Blueprint $table) {
            // ارجع زي الاول
            $table->unsignedBigInteger('marketer_id')->nullable(false)->change();

            $table->dropForeign(['employee_id']);
            $table->dropColumn('employee_id');
        });
    }
};