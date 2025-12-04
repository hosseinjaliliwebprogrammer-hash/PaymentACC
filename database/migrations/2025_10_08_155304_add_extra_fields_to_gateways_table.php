<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gateways', function (Blueprint $t) {
            $t->string('name')->nullable();
            $t->string('invoice_description')->nullable();
            $t->string('email_template_type')->nullable();
            $t->integer('max_transactions')->default(0);
            $t->string('logo')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('gateways', function (Blueprint $t) {
            $t->dropColumn([
                'name',
                'invoice_description',
                'email_template_type',
                'max_transactions',
                'logo',
            ]);
        });
    }
};
