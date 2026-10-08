<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->boolean('is_active')->default(true);
            $table->json('authorized_roles'); // Roles that can manage requests
            $table->json('form_fields'); // Dynamic form fields for company registration
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_types');
    }
}; 