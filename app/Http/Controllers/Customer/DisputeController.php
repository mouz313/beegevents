<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Dispute;
use Illuminate\Http\Request;

class DisputeController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'reason' => 'required|string|max:2000',
        ]);

        $booking = Booking::findOrFail($request->booking_id);

        if ($booking->customer_id !== auth()->id()) {
            abort(403);
        }

        if (! in_array($booking->status, ['verified', 'confirmed', 'completed'])) {
            return $this->respond('Disputes can only be raised for active or completed bookings.', 422);
        }

        $alreadyOpen = Dispute::where('booking_id', $booking->id)
            ->where('status', 'open')
            ->exists();

        if ($alreadyOpen) {
            return $this->respond('A dispute is already open for this booking.', 422);
        }

        $dispute = Dispute::create([
            'booking_id' => $booking->id,
            'raised_by' => auth()->id(),
            'reason' => $request->reason,
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'dispute' => $dispute]);
        }

        return redirect()->back()->with('success', 'Dispute raised. We will review it shortly.');
    }

    protected function respond(string $message, int $status)
    {
        if (request()->ajax()) {
            return response()->json(['success' => false, 'message' => $message], $status);
        }

        return redirect()->back()->with('error', $message);
    }
}
