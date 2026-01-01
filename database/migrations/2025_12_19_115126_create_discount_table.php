<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('discount_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->decimal('discount_percentage', 5, 2);
            $table->timestamp('start_date');
            $table->timestamp('end_date');
            $table->boolean('is_active')->default(1);
            $table->timestamps();
        });

        Schema::create('discount_code_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('discount_code_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('discount_code_product');
        Schema::dropIfExists('discount_codes');
    }
};
