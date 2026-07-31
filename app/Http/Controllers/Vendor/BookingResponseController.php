<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\BookingItem;
use Illuminate\Http\Request;

class BookingResponseController extends Controller
{
    public function index()
    {
        $profile = auth()->user()->vendorProfile;
        if (!$profile) return redirect()->route('vendor.dashboard');
        
        $items = BookingItem::where('vendor_profile_id', $profile->id)
            ->with('booking.customer')
            ->latest()
            ->paginate(20);
        
        return view('vendor.bookings.index', compact('items'));
    }

    public function respond(Request $request, BookingItem $bookingItem)
    {
        $request->validate(['status' => 'required|in:accepted,declined']);
        $bookingItem->update(['vendor_status' => $request->status]);

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }
        return redirect()->back()->with('success', 'Response recorded!');
    }
}
