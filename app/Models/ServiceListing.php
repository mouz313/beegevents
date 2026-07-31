<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

#[Fillable(['vendor_profile_id', 'service_category_id', 'title', 'description', 'price', 'price_unit'])]
class ServiceListing extends Model
{
    use HasFactory;

    /**
     * @return BelongsTo<VendorProfile>
     */
    public function vendorProfile(): BelongsTo
    {
        return $this->belongsTo(VendorProfile::class);
    }

    /**
     * @return BelongsTo<ServiceCategory>
     */
    public function serviceCategory(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class);
    }

    /**
     * @return MorphMany<ExtraService>
     */
    public function extraServices(): MorphMany
    {
        return $this->morphMany(ExtraService::class, 'serviceable');
    }

    /**
     * @return MorphMany<AvailabilitySlot>
     */
    public function availabilitySlots(): MorphMany
    {
        return $this->morphMany(AvailabilitySlot::class, 'resource');
    }
}
