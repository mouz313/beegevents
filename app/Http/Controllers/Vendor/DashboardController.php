<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use Illuminate\Http\Request;

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
        ];

        if ($profile) {
            $stats['total_inquiries'] = Inquiry::where('vendor_profile_id', $profile->id)->count();
            $stats['pending_inquiries'] = Inquiry::where('vendor_profile_id', $profile->id)->where('status', 'pending')->count();
            $stats['listings_count'] = $profile->halls()->count() + $profile->serviceListings()->count();
            $stats['active_bookings'] = \App\Models\BookingItem::where('vendor_profile_id', $profile->id)
                ->whereHas('booking', function($q) {
                    $q->whereIn('status', ['confirmed', 'verified']);
                })->count();
        }

        return view('vendor.dashboard', compact('profile', 'stats'));
    }
}
