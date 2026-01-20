<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
 public function up(): void
    {
        Schema::table('commissions', function (Blueprint $table) {
            $table->decimal('cheque_amount', 10, 2)->nullable()->after('invoice_image');
            $table->decimal('discount_rate', 5, 2)->nullable()->after('cheque_amount');
        });
    }

    public function down(): void
    {
        Schema::table('commissions', function (Blueprint $table) {
            $table->dropColumn(['cheque_amount', 'discount_rate']);
        });
    }
};