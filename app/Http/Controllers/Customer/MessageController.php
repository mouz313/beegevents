<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index(Booking $booking)
    {
        if ($booking->customer_id !== auth()->id()) {
            abort(403);
        }

        $booking->load('messages.user');
        return view('customer.messages.index', compact('booking'));
    }

    public function store(Request $request, Booking $booking)
    {
        if ($booking->customer_id !== auth()->id()) {
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
