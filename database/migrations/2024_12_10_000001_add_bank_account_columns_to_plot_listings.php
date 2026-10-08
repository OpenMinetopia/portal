<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plot_listings', function (Blueprint $table) {
            $table->string('payout_bank_account_uuid')->after('seller_id');
            $table->string('buyer_bank_account_uuid')->nullable()->after('payout_bank_account_uuid');
        });
    }

    public function down(): void
    {
        Schema::table('plot_listings', function (Blueprint $table) {
            $table->dropColumn(['payout_bank_account_uuid', 'buyer_bank_account_uuid']);
        });
    }
}; 