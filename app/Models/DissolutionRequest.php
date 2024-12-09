<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DissolutionRequest extends Model
{
    protected $fillable = [
        'company_id',
        'user_id',
        'status',
        'reason',
        'admin_notes',
        'handled_by',
        'handled_at'
    ];

    protected $casts = [
        'handled_at' => 'datetime',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function handler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isDenied(): bool
    {
        return $this->status === 'denied';
    }

    public function getStatusText(): string
    {
        return match ($this->status) {
            'pending' => 'In Behandeling',
            'approved' => 'Goedgekeurd',
            'denied' => 'Afgewezen',
            default => 'Onbekend'
        };
    }
} 