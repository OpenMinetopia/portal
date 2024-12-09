<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('plot_listings', function (Blueprint $table) {
            $table->integer('min_x')->after('description');
            $table->integer('min_y')->after('min_x');
            $table->integer('min_z')->after('min_y');
            $table->integer('max_x')->after('min_z');
            $table->integer('max_y')->after('max_x');
            $table->integer('max_z')->after('max_y');
            $table->boolean('instant_buy')->default(true)->after('max_z');
        });
    }

    public function down()
    {
        Schema::table('plot_listings', function (Blueprint $table) {
            $table->dropColumn(['min_x', 'min_y', 'min_z', 'max_x', 'max_y', 'max_z', 'instant_buy']);
        });
    }
}; 