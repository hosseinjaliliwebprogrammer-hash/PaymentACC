<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('gateways', function (Blueprint $table) {
            // نام نمایشی حساب (مثلاً PayPal A)
            $table->string('display_name')->nullable()->after('email');

            // نوع تحویل اطلاعات پرداخت (email یا url)
            $table->enum('delivery_mode', ['email', 'url'])->default('email')->after('display_name');

            // لینک پرداخت، اگر delivery_mode = url باشد
            $table->string('payment_url')->nullable()->after('delivery_mode');
        });
    }

    public function down(): void
    {
        Schema::table('gateways', function (Blueprint $table) {
            $table->dropColumn(['display_name', 'delivery_mode', 'payment_url']);
        });
    }
};
