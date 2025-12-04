<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('gateways', function (Blueprint $table) {
            $table->id();
            $table->string('email');                 // ایمیل پی‌پال
            $table->string('link')->nullable();      // لینک اختیاری
            $table->decimal('limit_amount', 12, 2)->default(0); // 0 یعنی بدون سقف
            $table->decimal('used_amount', 12, 2)->default(0);  // مجموع مصرف‌شده
            $table->boolean('is_active')->default(true);        // فعال/غیرفعال
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('gateways');
    }
};

