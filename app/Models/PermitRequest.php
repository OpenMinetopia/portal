<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PermitRequest extends Model
{
    protected $fillable = [
        'permit_type_id',
        'user_id',
        'handled_by',
        'status',
        'form_data',
        'admin_notes',
        'handled_at'
    ];

    protected $casts = [
        'form_data' => 'array',
        'handled_at' => 'datetime'
    ];

    public function type(): BelongsTo
    {
        return $this->belongsTo(PermitType::class, 'permit_type_id');
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
} 