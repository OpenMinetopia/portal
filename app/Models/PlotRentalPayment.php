<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlotRentalPayment extends Model
{
    protected $fillable = [
        'plot_rental_id',
        'period_start',
        'period_end',
        'amount',
        'bank_account_uuid',
        'status',
        'paid_at',
        'payout_status',
    ];

    protected $casts = [
        'period_start' => 'datetime',
        'period_end' => 'datetime',
        'paid_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function rental(): BelongsTo
    {
        return $this->belongsTo(PlotRental::class, 'plot_rental_id');
    }
}
