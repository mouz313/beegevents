<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\AvailabilitySlot;
use App\Mail\BookingStatusMail;
use App\Services\CancellationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class BookingController extends Controller
{
    protected CancellationService $cancellationService;

    public function __construct(CancellationService $cancellationService)
    {
        $this->cancellationService = $cancellationService;
    }

    public function checkout()
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('customer.cart')->with('error', 'Your cart is empty.');
        }
        return view('customer.checkout', compact('cart'));
    }

    public function store(Request $request)
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return response()->json(['success' => false, 'message' => 'Cart is empty'], 400);
        }

        $request->validate([
            'event_date' => 'required|date|after:today',
            'event_type' => 'required|in:wedding,engagement,corporate,birthday,home,other',
            'notes' => 'nullable|string|max:1000',
        ]);

        $totalPrice = collect($cart)->sum('price');
        $bookingType = count($cart) > 1 ? 'multi' : 'single';

        $booking = Booking::create([
            'customer_id' => auth()->id(),
            'booking_type' => $bookingType,
            'event_date' => $request->event_date,
            'event_type' => $request->event_type,
            'total_price' => $totalPrice,
            'notes' => $request->notes,
        ]);

        foreach ($cart as $key => $item) {
            $itemableType = $item['type'] === 'hall_unit' ? 'App\Models\HallUnit' : 'App\Models\ServiceListing';

            $bookingItem = BookingItem::create([
                'booking_id' => $booking->id,
                'itemable_type' => $itemableType,
                'itemable_id' => $item['id'],
                'vendor_profile_id' => $item['vendor_id'],
                'price' => $item['price'],
            ]);

            if ($item['type'] === 'hall_unit') {
                AvailabilitySlot::create([
                    'resource_type' => 'App\Models\HallUnit',
                    'resource_id' => $item['id'],
                    'date' => $request->event_date,
                    'status' => 'held',
                    'booking_id' => $booking->id,
                ]);
            }
        }

        session()->forget('cart');

        if ($request->ajax()) {
            return response()->json(['success' => true, 'booking_id' => $booking->id]);
        }
        return redirect()->route('customer.bookings.show', $booking)->with('success', 'Booking created!');
    }

    public function show(Booking $booking)
    {
        $booking->load('bookingItems');
        $refundInfo = null;
        if (in_array($booking->status, ['requested', 'discussing', 'verified', 'confirmed'])) {
            $refundInfo = $this->cancellationService->calculateRefund($booking);
        }
        return view('customer.bookings.show', compact('booking', 'refundInfo'));
    }

    public function index()
    {
        $bookings = auth()->user()->bookings()->with('bookingItems')->latest()->paginate(10);
        return view('customer.bookings.index', compact('bookings'));
    }

    public function cancel(Request $request, Booking $booking)
    {
        if ($booking->customer_id !== auth()->id()) {
            abort(403);
        }

        if (!in_array($booking->status, ['requested', 'discussing', 'verified', 'confirmed'])) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'This booking cannot be cancelled.'], 422);
            }
            return redirect()->back()->with('error', 'This booking cannot be cancelled.');
        }

        $refundInfo = $this->cancellationService->cancel($booking);

        try {
            Mail::to($booking->customer->email)->send(new BookingStatusMail(
                $booking,
                'Booking Cancelled',
                'Hi '.$booking->customer->name.',',
                'Your booking #'.$booking->id.' has been cancelled. Refund: PKR '.number_format($refundInfo['refund_amount']).'.',
                route('customer.bookings.show', $booking)
            ));
        } catch (\Exception $e) {
            \Log::warning('Cancel notification failed: '.$e->getMessage());
        }

        if ($request->ajax()) {
            return response()->json(['success' => true, 'refund' => $refundInfo]);
        }
        return redirect()->route('customer.bookings.show', $booking)->with('success', 'Booking cancelled. Refund: PKR '.number_format($refundInfo['refund_amount']));
    }
}
