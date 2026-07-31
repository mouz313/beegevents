<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['vendor_profile_id', 'name', 'address', 'description', 'has_floors'])]
class Hall extends Model
{
    use HasFactory;

    public function vendorProfile(): BelongsTo
    {
        return $this->belongsTo(VendorProfile::class);
    }

    public function floors(): HasMany
    {
        return $this->hasMany(Floor::class);
    }

    public function hallUnits(): HasMany
    {
        return $this->hasMany(HallUnit::class);
    }

    public function hallImages(): HasMany
    {
        return $this->hasMany(HallImage::class)->orderBy('sort_order');
    }

    public function getCoverImageAttribute(): ?string
    {
        $first = $this->hallImages->first();
        return $first ? $first->image_path : null;
    }
}
