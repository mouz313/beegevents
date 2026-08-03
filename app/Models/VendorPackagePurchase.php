<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['vendor_profile_id', 'package_id', 'amount', 'method', 'status', 'duration_days', 'max_halls', 'max_listings', 'boost_tier', 'starts_at', 'ends_at', 'paid_at', 'transaction_id', 'proof_path', 'notes'])]
class VendorPackagePurchase extends Model
{
    use HasFactory;

    protected $table = 'vendor_package_purchases';

    protected $casts = [
        'amount' => 'float',
        'duration_days' => 'integer',
        'max_halls' => 'integer',
        'max_listings' => 'integer',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public function vendorProfile(): BelongsTo
    {
        return $this->belongsTo(VendorProfile::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active'
            && $this->ends_at !== null
            && $this->ends_at->gt(now());
    }
}
