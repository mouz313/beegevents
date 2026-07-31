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

        $commissionShare = $items->sum(function (BookingItem $item) {
            $booking = $item->booking;
            if ((float) $booking->total_price <= 0) {
                return 0;
            }

            return $booking->commission_amount * ($item->price / $booking->total_price);
        });

        $net = $gross - $commissionShare;

        $reserved = Payout::where('vendor_profile_id', $vendor->id)
            ->whereIn('status', ['pending', 'processed'])
            ->sum('amount');

        return max(0, round($net - $reserved, 2));
    }

    public function totalEarned(VendorProfile $vendor): float
    {
        $items = BookingItem::where('vendor_profile_id', $vendor->id)
            ->whereHas('booking', fn ($q) => $q->where('status', 'completed'))
            ->with('booking')
            ->get();

        $gross = $items->sum('price');

        $commissionShare = $items->sum(function (BookingItem $item) {
            $booking = $item->booking;
            if ((float) $booking->total_price <= 0) {
                return 0;
            }

            return $booking->commission_amount * ($item->price / $booking->total_price);
        });

        return round($gross - $commissionShare, 2);
    }
}
