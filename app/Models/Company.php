<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Company extends Model
{
    protected $fillable = [
        'name',
        'type_id',
        'owner_id',
        'kvk_number',
        'description',
        'data',
        'is_active',
        'slug',
        'company_request_id',
        'dissolution_requested_at'
    ];

    protected $casts = [
        'data' => 'array',
        'is_active' => 'boolean',
        'dissolution_requested_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($company) {
            $company->slug = Str::slug($company->name);
            $company->kvk_number = static::generateKvkNumber();
        });
    }

    public static function generateKvkNumber(): string
    {
        do {
            $number = mt_rand(10000000, 99999999);
        } while (static::where('kvk_number', $number)->exists());

        return (string) $number;
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function type()
    {
        return $this->belongsTo(CompanyType::class, 'type_id');
    }

    public function dissolutionRequest()
    {
        return $this->hasOne(DissolutionRequest::class)->where('status', 'pending');
    }

    public function hasPendingDissolution(): bool
    {
        return $this->dissolutionRequest()->exists();
    }
}
