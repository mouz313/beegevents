<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['vendor_profile_id', 'package_purchase_id', 'package_id', 'is_active'])]
class VendorCombo extends Model
{
    use HasFactory;

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function vendorProfile(): BelongsTo
    {
        return $this->belongsTo(VendorProfile::class);
    }

    public function packagePurchase(): BelongsTo
    {
        return $this->belongsTo(VendorPackagePurchase::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(VendorComboItem::class, 'combo_id')->orderBy('slot_index')->with('itemable');
    }

    /**
     * The template slots that must be filled, in order.
     */
    public function templateSlots(): \Illuminate\Support\Collection
    {
        return $this->package?->packageItems ?? collect();
    }

    public function isFullyFilled(): bool
    {
        return $this->items()->count() >= $this->templateSlots()->count();
    }

    /**
     * Total price is the sum of the vendor's chosen item prices.
     */
    public function getTotalPriceAttribute(): float
    {
        return round($this->items->sum(function ($comboItem) {
            $item = $comboItem->itemable;

            return (float) ($item->base_price ?? $item->price ?? 0);
        }), 2);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
