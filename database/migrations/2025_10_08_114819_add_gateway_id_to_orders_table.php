<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('orders', function (Blueprint $table) {
            // اگر قبلاً ستون نداری:
            $table->foreignId('gateway_id')
                  ->nullable()
                  ->constrained('gateways')
                  ->nullOnDelete()
                  ->index();
        });
    }

    public function down(): void {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('gateway_id');
        });
    }
};
