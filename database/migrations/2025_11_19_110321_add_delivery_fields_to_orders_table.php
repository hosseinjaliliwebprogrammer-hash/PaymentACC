<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('delivery_username')->nullable();
            $table->string('delivery_password')->nullable();
            $table->string('delivery_server')->nullable();
            $table->text('delivery_notes')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'delivery_username',
                'delivery_password',
                'delivery_server',
                'delivery_notes',
            ]);
        });
    }
};
