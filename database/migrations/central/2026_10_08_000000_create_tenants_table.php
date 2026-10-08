<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            // The website's instance tenant_id.
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('status', 16)->default('active');

            $table->string('tenancy_db_name');
            $table->string('tenancy_db_username')->nullable();
            $table->text('tenancy_db_password')->nullable();

            $table->string('plugin_api_url')->nullable();
            $table->text('plugin_api_key')->nullable();
            $table->text('minecraft_api_key')->nullable();
            $table->string('server_address')->nullable();

            $table->string('admin_claim_token_hash', 64)->nullable();
            $table->timestamp('admin_claim_expires_at')->nullable();

            $table->timestamps();
            $table->json('data')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
