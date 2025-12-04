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
    Schema::create('requests', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
        $table->string('name');
        $table->string('email')->index();
        $table->string('service');
        $table->decimal('amount', 10, 2)->nullable();
        $table->text('description')->nullable();
        $table->enum('status', ['pending', 'approved', 'rejected', 'completed'])->default('pending')->index();
        $table->string('tracking_code')->unique();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requests');
    }
};
