<?php

namespace App\Services;

use App\Models\BookingItem;
use App\Models\Payout;
use App\Models\VendorProfile;

class PayoutService
{
    public function calculateDue(VendorProfile $vendor): float
    {
        $items = BookingItem::where('vendor_profile_id', $vendor->id)
            ->whereHas('booking', fn ($q) => $q->where('status', 'completed'))
            ->with('booking')
            ->get();

        $gross = $items->sum('price');

        $reserved = Payout::where('vendor_profile_id', $vendor->id)
            ->whereIn('status', ['pending', 'processed'])
            ->sum('amount');

        return max(0, round($gross - $reserved, 2));
    }

    public function totalEarned(VendorProfile $vendor): float
    {
        $items = BookingItem::where('vendor_profile_id', $vendor->id)
            ->whereHas('booking', fn ($q) => $q->where('status', 'completed'))
            ->with('booking')
            ->get();

        return round($items->sum('price'), 2);
    }
}
