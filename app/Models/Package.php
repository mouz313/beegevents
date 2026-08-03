<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['title', 'description', 'total_price', 'duration_days', 'boost_tier', 'max_halls', 'max_listings', 'is_active'])]
class Package extends Model
{
    use HasFactory;

    protected $casts = [
        'total_price' => 'float',
        'duration_days' => 'integer',
        'max_halls' => 'integer',
        'max_listings' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * @return HasMany<PackageItem>
     */
    public function packageItems(): HasMany
    {
        return $this->hasMany(PackageItem::class);
    }

    public function combos(): HasMany
    {
        return $this->hasMany(VendorCombo::class);
    }

    public function isActive(): bool
    {
        return (bool) $this->is_active;
    }
}
