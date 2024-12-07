<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $fillable = [
        'bank_account_id',
        'amount',
        'type',
        'description'
    ];

    protected $casts = [
        'amount' => 'integer'
    ];

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    // Helper methods
    public function isDeposit(): bool
    {
        return $this->type === 'deposit';
    }

    public function isWithdrawal(): bool
    {
        return $this->type === 'withdraw';
    }

    public function getFormattedAmount(): string
    {
        $prefix = $this->isDeposit() ? '+' : '-';
        return $prefix . '$' . number_format($this->amount);
    }
}
