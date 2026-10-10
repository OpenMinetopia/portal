<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // A plot offered for rent at the makelaar, next to or instead of a sale listing.
        Schema::create('plot_rental_offers', function (Blueprint $table) {
            $table->id();
            $table->string('plot_name');
            $table->foreignId('landlord_id')->constrained('users');
            $table->boolean('is_server_listing')->default(false);
            $table->string('payout_bank_account_uuid')->nullable();
            $table->decimal('price', 10, 2);
            $table->enum('period', ['week', 'month']);
            $table->text('description');
            $table->string('image_path')->nullable();
            $table->integer('min_x');
            $table->integer('min_y');
            $table->integer('min_z');
            $table->integer('max_x');
            $table->integer('max_y');
            $table->integer('max_z');
            $table->enum('status', ['active', 'rented', 'cancelled'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['plot_name', 'status']);
        });

        // One rental contract. Price, period and payout are copied from the offer when it starts.
        Schema::create('plot_rentals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plot_rental_offer_id')->constrained('plot_rental_offers');
            $table->string('plot_name');
            $table->foreignId('landlord_id')->constrained('users');
            $table->foreignId('renter_id')->constrained('users');
            $table->boolean('is_server_listing')->default(false);
            $table->string('payout_bank_account_uuid')->nullable();
            $table->string('renter_bank_account_uuid');
            $table->decimal('price', 10, 2);
            $table->enum('period', ['week', 'month']);
            $table->enum('status', ['active', 'ended'])->default('active');
            $table->dateTime('started_at');
            $table->dateTime('paid_until');
            // Set when the renter or landlord gives notice: the rental ends when the paid period does.
            $table->dateTime('ends_at')->nullable();
            $table->string('cancelled_by')->nullable();
            // Set while a renewal is unpaid: the renter keeps the plot until grace_until.
            $table->dateTime('overdue_since')->nullable();
            $table->dateTime('grace_until')->nullable();
            $table->dateTime('ended_at')->nullable();
            $table->string('end_reason')->nullable();
            $table->dateTime('access_revoked_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'paid_until']);
        });

        // Every rent payment. The unique period start is what keeps a rerun from charging twice.
        Schema::create('plot_rental_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plot_rental_id')->constrained('plot_rentals')->cascadeOnDelete();
            $table->dateTime('period_start');
            $table->dateTime('period_end');
            $table->decimal('amount', 10, 2);
            $table->string('bank_account_uuid');
            // pending while the withdrawal runs; a pending row is never charged again.
            $table->enum('status', ['pending', 'paid'])->default('pending');
            $table->dateTime('paid_at')->nullable();
            // null without a payout account; pending while the deposit runs, failed is retried.
            $table->enum('payout_status', ['pending', 'done', 'failed'])->nullable();
            $table->timestamps();

            $table->unique(['plot_rental_id', 'period_start']);
        });

        // The makelaar now also rents out: refresh the feature and permission texts.
        \App\Support\Features::sync();
        \App\Support\Permissions::sync();
    }

    public function down(): void
    {
        Schema::dropIfExists('plot_rental_payments');
        Schema::dropIfExists('plot_rentals');
        Schema::dropIfExists('plot_rental_offers');
    }
};
