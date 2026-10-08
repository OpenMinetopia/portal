<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->foreignId('type_id')->constrained('company_types');
            $table->foreignId('owner_id')->constrained('users');
            $table->string('kvk_number')->unique();
            $table->text('description')->nullable();
            $table->json('data')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('company_request_id')->constrained();
            $table->timestamp('dissolution_requested_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
