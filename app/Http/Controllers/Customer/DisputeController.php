<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
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

        $dispute = Dispute::create([
            'booking_id' => $request->booking_id,
            'raised_by' => auth()->id(),
            'reason' => $request->reason,
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'dispute' => $dispute]);
        }
        return redirect()->back()->with('success', 'Dispute raised. We will review it shortly.');
    }
}
