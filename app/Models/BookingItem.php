<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Fillable(['booking_id', 'itemable_type', 'itemable_id', 'vendor_profile_id', 'price', 'time_slot', 'extras', 'menu_set_id', 'guests', 'catering_mode', 'vendor_status'])]
class BookingItem extends Model
{
    use HasFactory;

    protected $casts = [
        'extras' => 'array',
        'guests' => 'integer',
    ];

    /**
     * @return BelongsTo<Booking>
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * @return MorphTo
     */
    public function itemable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return BelongsTo<VendorProfile>
     */
    public function vendorProfile(): BelongsTo
    {
        return $this->belongsTo(VendorProfile::class);
    }

    /**
     * @return BelongsTo<MenuSet>
     */
    public function menuSet(): BelongsTo
    {
        return $this->belongsTo(MenuSet::class);
    }
}
