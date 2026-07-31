<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Message;
use App\Services\NotificationService;
use App\Services\RealtimeChatService;
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

        $clientId = $request->input('client_id');

        $message = $clientId
            ? Message::firstOrCreate(
                ['booking_id' => $booking->id, 'client_id' => $clientId],
                ['user_id' => auth()->id(), 'message' => $request->message]
            )
            : Message::create([
                'booking_id' => $booking->id,
                'user_id' => auth()->id(),
                'message' => $request->message,
            ]);

        if ($message->wasRecentlyCreated) {
            app(RealtimeChatService::class)->publish($message);

            app(NotificationService::class)->notifyParticipants(
                $booking,
                auth()->id(),
                'New message from '.auth()->user()->name,
                $message->message,
            );
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => [
                    'id' => $message->id,
                    'user_id' => $message->user_id,
                    'name' => $message->user?->name ?? 'Unknown',
                    'message' => $message->message,
                    'created_at' => (int) $message->created_at->getTimestampMs(),
                ],
            ]);
        }

        return redirect()->back()->with('success', 'Message sent!');
    }
}
