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
        Schema::create('paypal_payment_submissions', function (Blueprint $table) {
    $table->id();

    // ارتباط با کاربر
    $table->foreignId('user_id')->constrained()->onDelete('cascade');

    // ارتباط با سفارش
    $table->foreignId('order_id')->constrained()->onDelete('cascade');

    // اطلاعات پرداخت
    $table->string('transaction_id')->nullable(); 
    $table->string('paypal_email')->nullable();
    $table->string('screenshot_path')->nullable();

    // وضعیت پرداخت (pending / approved / rejected)
    $table->string('status')->default('pending');

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paypal_payment_submissions');
    }
};
