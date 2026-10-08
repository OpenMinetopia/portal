<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('plot_listings', function (Blueprint $table) {
            $table->id();
            $table->string('plot_name');
            $table->foreignId('seller_id')->constrained('users');
            $table->decimal('price', 10, 2);
            $table->text('description');
            $table->string('image_path')->nullable();
            $table->enum('status', ['active', 'sold', 'cancelled'])->default('active');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('plot_listings');
    }
}; 