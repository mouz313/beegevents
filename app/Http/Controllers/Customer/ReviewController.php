<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'vendor_profile_id' => 'required|exists:vendor_profiles,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $booking = Booking::with('bookingItems')->findOrFail($request->booking_id);

        if ($booking->customer_id !== auth()->id()) {
            abort(403);
        }

        if ($booking->status !== 'completed') {
            return $this->respond('You can only review a booking after it has been completed.', 422);
        }

        $vendorInBooking = $booking->bookingItems->contains('vendor_profile_id', (int) $request->vendor_profile_id);
        if (! $vendorInBooking) {
            return $this->respond('You can only review vendors that were part of this booking.', 422);
        }

        $alreadyReviewed = Review::where('booking_id', $booking->id)
            ->where('customer_id', auth()->id())
            ->where('vendor_profile_id', $request->vendor_profile_id)
            ->exists();

        if ($alreadyReviewed) {
            return $this->respond('You have already reviewed this vendor for this booking.', 422);
        }

        $review = Review::create([
            'booking_id' => $booking->id,
            'customer_id' => auth()->id(),
            'vendor_profile_id' => $request->vendor_profile_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'review' => $review]);
        }

        return redirect()->back()->with('success', 'Review submitted!');
    }

    protected function respond(string $message, int $status)
    {
        if (request()->ajax()) {
            return response()->json(['success' => false, 'message' => $message], $status);
        }

        return redirect()->back()->with('error', $message);
    }
}
