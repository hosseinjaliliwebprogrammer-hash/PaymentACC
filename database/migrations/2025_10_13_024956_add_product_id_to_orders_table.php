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
        Schema::table('orders', function (Blueprint $table) {
        $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
        // اگر قبلاً ستون service داری و می‌خوای نگه داری، بماند.
        // اگر نمی‌خوای: $table->dropColumn('service'); را در down بگذار.
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
        $table->dropConstrainedForeignId('product_id');
    });
    }
};
