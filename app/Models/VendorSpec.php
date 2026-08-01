<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Dynamic model that maps onto one of the per-type spec tables
 * ({type}_specs) depending on the vendor type.
 *
 * Always call ->setTable("{$type}_specs") before querying.
 */
class VendorSpec extends Model
{
    protected $guarded = [];

    protected $casts = [
        'amenities' => 'array',
        'styles' => 'array',
        'cuisine_types' => 'array',
        'coverage_types' => 'array',
        'equipment' => 'array',
        'vehicle_types' => 'array',
    ];

    public function vendorProfile(): BelongsTo
    {
        return $this->belongsTo(VendorProfile::class);
    }

    public static function forProfile(VendorProfile $profile): ?self
    {
        $type = $profile->vendor_type;
        if (!$type || !array_key_exists($type, config('vendor-specs.types', []))) {
            return null;
        }

        $spec = new self;
        $spec->setTable($type . '_specs');

        return $spec->where('vendor_profile_id', $profile->id)->first();
    }
}
