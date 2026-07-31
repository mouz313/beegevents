<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index(Booking $booking)
    {
        $hasAccess = $booking->bookingItems()
            ->where('vendor_profile_id', auth()->user()->vendorProfile?->id)
            ->exists();

        if (!$hasAccess) {
            abort(403);
        }

        $booking->load('messages.user');
        return view('vendor.messages.index', compact('booking'));
    }

    public function store(Request $request, Booking $booking)
    {
        $hasAccess = $booking->bookingItems()
            ->where('vendor_profile_id', auth()->user()->vendorProfile?->id)
            ->exists();

        if (!$hasAccess) {
            abort(403);
        }

        $request->validate(['message' => 'required|string|max:2000']);

        Message::create([
            'booking_id' => $booking->id,
            'user_id' => auth()->id(),
            'message' => $request->message,
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Message sent!');
    }
}
