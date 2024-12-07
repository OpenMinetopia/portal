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
        Schema::create('level_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->integer('points_from_plots')->default(0);
            $table->integer('points_from_balance')->default(0);
            $table->integer('points_from_vehicles')->default(0);
            $table->integer('points_from_prefix')->default(0);
            $table->integer('points_from_playtime')->default(0);
            $table->integer('points_from_fitness')->default(0);
            $table->timestamp('last_calculated')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('level_progress');
    }
};
