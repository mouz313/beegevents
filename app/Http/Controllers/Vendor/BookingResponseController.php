<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Mail\BookingStatusMail;
use App\Models\AvailabilitySlot;
use App\Models\BookingItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class BookingResponseController extends Controller
{
    public function index()
    {
        $profile = auth()->user()->vendorProfile;
        if (! $profile) {
            return redirect()->route('vendor.dashboard');
        }

        $items = BookingItem::where('vendor_profile_id', $profile->id)
            ->with('booking.customer')
            ->latest()
            ->paginate(20);

        return view('vendor.bookings.index', compact('items'));
    }

    public function respond(Request $request, BookingItem $bookingItem)
    {
        $profile = auth()->user()->vendorProfile;

        if (! $profile || $bookingItem->vendor_profile_id !== $profile->id) {
            abort(403);
        }

        $request->validate(['status' => 'required|in:accepted,declined']);

        if ($bookingItem->vendor_status !== 'pending') {
            return $this->errorResponse($request, 'You have already responded to this booking request.', 422);
        }

        $booking = $bookingItem->booking;

        DB::transaction(function () use ($bookingItem, $request, $booking) {
            $bookingItem->update(['vendor_status' => $request->status]);

            $statuses = $booking->bookingItems()->pluck('vendor_status');

            if ($statuses->isNotEmpty() && $statuses->every(fn ($s) => $s === 'accepted')) {
                if ($booking->status === 'requested') {
                    $booking->update(['status' => 'discussing']);
                }
            } elseif ($statuses->every(fn ($s) => $s === 'declined')) {
                $booking->update(['status' => 'cancelled']);

                AvailabilitySlot::where('booking_id', $booking->id)
                    ->whereIn('status', ['held', 'booked'])
                    ->update(['status' => 'available', 'booking_id' => null, 'held_until' => null]);
            }
        });

        $this->notifyCustomer($booking, $request->status);

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Response recorded!');
    }

    protected function notifyCustomer($booking, string $status): void
    {
        try {
            $verb = $status === 'accepted' ? 'accepted' : 'declined';
            Mail::to($booking->customer->email)->send(new BookingStatusMail(
                $booking,
                'Vendor '.$verb.' your request #'.$booking->id,
                'Hi '.$booking->customer->name.',',
                'A vendor has '.$verb.' your booking request. Check your booking for the latest status.',
                route('customer.bookings.show', $booking)
            ));
        } catch (\Exception $e) {
            \Log::warning('Vendor response notification failed: '.$e->getMessage());
        }
    }

    protected function errorResponse(Request $request, string $message, int $status)
    {
        if ($request->ajax()) {
            return response()->json(['success' => false, 'message' => $message], $status);
        }

        return redirect()->back()->with('error', $message);
    }
}
