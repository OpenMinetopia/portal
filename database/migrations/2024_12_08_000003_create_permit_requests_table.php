<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permit_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permit_type_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('handled_by')->nullable()->constrained('users');
            $table->string('status')->default('pending'); // pending, approved, denied
            $table->json('form_data');
            $table->text('admin_notes')->nullable();
            $table->timestamp('handled_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permit_requests');
    }
}; 