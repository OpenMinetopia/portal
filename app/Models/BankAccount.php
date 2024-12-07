<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BankAccount extends Model
{
    protected $fillable = [
        'user_id',
        'balance',
        'last_transaction'
    ];

    protected $casts = [
        'balance' => 'integer',
        'last_transaction' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    // Helper methods
    public function deposit(int $amount, string $reason = null): bool
    {
        $this->balance += $amount;
        $this->last_transaction = now();
        
        if ($this->save()) {
            $this->transactions()->create([
                'amount' => $amount,
                'type' => 'deposit',
                'description' => $reason ?? 'Deposit'
            ]);
            return true;
        }
        return false;
    }

    public function withdraw(int $amount, string $reason = null): bool
    {
        if ($this->balance < $amount) {
            return false;
        }

        $this->balance -= $amount;
        $this->last_transaction = now();
        
        if ($this->save()) {
            $this->transactions()->create([
                'amount' => $amount,
                'type' => 'withdraw',
                'description' => $reason ?? 'Withdrawal'
            ]);
            return true;
        }
        return false;
    }
}
