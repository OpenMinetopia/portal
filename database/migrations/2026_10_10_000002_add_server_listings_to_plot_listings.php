<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plot_listings', function (Blueprint $table) {
            $table->boolean('is_server_listing')->default(false)->after('seller_id');
            $table->string('payout_bank_account_uuid')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('plot_listings', function (Blueprint $table) {
            $table->dropColumn('is_server_listing');
        });
    }
};
