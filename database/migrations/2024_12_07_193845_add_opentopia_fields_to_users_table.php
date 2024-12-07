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
        Schema::table('users', function (Blueprint $table) {
            $table->integer('level')->default(1);
            $table->integer('calculated_level')->default(1);
            $table->integer('playtime')->default(0);
            $table->string('default_prefix')->nullable();
            $table->string('prefix_color')->nullable();
            $table->string('level_color')->nullable();
            $table->string('name_color')->nullable();
            $table->string('chat_color')->nullable();
            $table->timestamp('last_login')->nullable();
            $table->timestamp('last_logout')->nullable();
            $table->boolean('is_online')->default(false);
            $table->integer('health_statistic')->default(100);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'level',
                'calculated_level',
                'playtime',
                'default_prefix',
                'prefix_color',
                'level_color',
                'name_color',
                'chat_color',
                'last_login',
                'last_logout',
                'is_online',
                'health_statistic',
            ]);
        });
    }
};
