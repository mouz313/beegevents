<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\BookingItem;
use App\Models\Inquiry;
use App\Models\Payout;
use App\Services\PayoutService;

class DashboardController extends Controller
{
    public function index()
    {
        $profile = auth()->user()->vendorProfile;

        $stats = [
            'total_inquiries' => 0,
            'pending_inquiries' => 0,
            'listings_count' => 0,
            'active_bookings' => 0,
            'total_earned' => 0,
            'payout_due' => 0,
            'payouts_paid' => 0,
        ];

        if ($profile) {
            $stats['total_inquiries'] = Inquiry::where('vendor_profile_id', $profile->id)->count();
            $stats['pending_inquiries'] = Inquiry::where('vendor_profile_id', $profile->id)->where('status', 'pending')->count();
            $stats['listings_count'] = $profile->halls()->count() + $profile->serviceListings()->count();
            $stats['active_bookings'] = BookingItem::where('vendor_profile_id', $profile->id)
                ->whereHas('booking', function ($q) {
                    $q->whereIn('status', ['confirmed', 'verified']);
                })->count();

            $payoutService = app(PayoutService::class);
            $stats['total_earned'] = $payoutService->totalEarned($profile);
            $stats['payout_due'] = $payoutService->calculateDue($profile);
            $stats['payouts_paid'] = Payout::where('vendor_profile_id', $profile->id)
                ->where('status', 'processed')
                ->sum('amount');
        }

        return view('vendor.dashboard', compact('profile', 'stats'));
    }
}
