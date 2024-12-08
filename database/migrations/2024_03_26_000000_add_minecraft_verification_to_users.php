<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('minecraft_verified')->default(false);
            $table->string('minecraft_uuid')->nullable()->unique();
            $table->uuid('minecraft_plain_uuid')->nullable()->unique();
            $table->timestamp('minecraft_verified_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['minecraft_verified', 'minecraft_uuid', 'minecraft_verified_at']);
        });
    }
};
