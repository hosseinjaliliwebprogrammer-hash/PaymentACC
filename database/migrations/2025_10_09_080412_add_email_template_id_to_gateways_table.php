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
    Schema::table('gateways', function (Blueprint $table) {
        if (! Schema::hasColumn('gateways', 'email_template_id')) {
            $table->foreignId('email_template_id')
                ->nullable()
                ->after('email_template_type')
                ->constrained('email_templates')
                ->nullOnDelete();
        }
    });
}

public function down(): void
{
    Schema::table('gateways', function (Blueprint $table) {
        if (Schema::hasColumn('gateways', 'email_template_id')) {
            try {
                $table->dropConstrainedForeignId('email_template_id');
            } catch (\Throwable $e) {
                $table->dropForeign(['email_template_id']);
                $table->dropColumn('email_template_id');
            }
        }
    });
}

};
