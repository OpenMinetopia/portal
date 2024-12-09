<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyRequest extends Model
{
    protected $fillable = [
        'company_type_id',
        'user_id',
        'name',
        'form_data',
        'status',
        'admin_notes',
        'handled_by',
        'handled_at'
    ];

    protected $casts = [
        'form_data' => 'array',
        'handled_at' => 'datetime',
    ];

    public function type(): BelongsTo
    {
        return $this->belongsTo(CompanyType::class, 'company_type_id');
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

    public function getGeneratedCoCNumber(): string
    {
        // If request is approved and company exists, return the stored KvK number
        if ($this->isApproved() && $this->company()->exists()) {
            return $this->company->kvk_number;
        }

        // Otherwise generate a preview number
        $typeId = str_pad($this->company_type_id, 2, '0', STR_PAD_LEFT);
        $random = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        return "KVK-{$typeId}{$random}";
    }

    // Add relationship to Company
    public function company()
    {
        return $this->hasOne(Company::class);
    }
} 