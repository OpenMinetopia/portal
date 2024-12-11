<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('company_requests', function (Blueprint $table) {
            $table->string('bank_account_uuid')->after('form_data');
            $table->decimal('price', 10, 2)->after('bank_account_uuid');
            $table->boolean('refunded')->default(false)->after('handled_at');
        });
    }

    public function down(): void
    {
        Schema::table('company_requests', function (Blueprint $table) {
            $table->dropColumn(['bank_account_uuid', 'price', 'refunded']);
        });
    }
}; 