<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
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

        $review = Review::create([
            'booking_id' => $request->booking_id,
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
}
