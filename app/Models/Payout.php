<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['vendor_profile_id', 'booking_id', 'amount', 'method', 'status', 'processed_at', 'notes'])]
class Payout extends Model
{
    use HasFactory;

    protected $casts = [
        'processed_at' => 'datetime',
    ];

    /**
     * @return BelongsTo<VendorProfile>
     */
    public function vendorProfile(): BelongsTo
    {
        return $this->belongsTo(VendorProfile::class);
    }

    /**
     * @return BelongsTo<Booking>
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
