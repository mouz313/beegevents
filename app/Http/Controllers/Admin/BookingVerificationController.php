<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\BookingStatusMail;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\AvailabilitySlot;
use App\Services\CancellationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class BookingVerificationController extends Controller
{
    protected CancellationService $cancellationService;

    public function __construct(CancellationService $cancellationService)
    {
        $this->cancellationService = $cancellationService;
    }

    public function index()
    {
        $bookings = Booking::with('customer', 'bookingItems')->latest()->paginate(20);
        return view('admin.bookings.index', compact('bookings'));
    }

    public function show(Booking $booking)
    {
        $booking->load('customer', 'bookingItems.vendorProfile', 'payments');
        return view('admin.bookings.show', compact('booking'));
    }

    public function verify(Request $request, Booking $booking)
    {
        $booking->update(['status' => 'verified']);
        $this->notifyCustomer($booking, 'Booking Verified', 'Hi '.$booking->customer->name.',', 'Your booking #'.$booking->id.' has been verified. We will confirm shortly.', route('customer.bookings.show', $booking));
        if ($request->ajax()) return response()->json(['success' => true]);
        return redirect()->back()->with('success', 'Booking verified!');
    }

    public function confirm(Request $request, Booking $booking)
    {
        $booking->update(['status' => 'confirmed']);
        $this->notifyCustomer($booking, 'Booking Confirmed', 'Hi '.$booking->customer->name.',', 'Your booking #'.$booking->id.' has been confirmed! We look forward to serving you.', route('customer.bookings.show', $booking));
        if ($request->ajax()) return response()->json(['success' => true]);
        return redirect()->back()->with('success', 'Booking confirmed!');
    }

    public function complete(Request $request, Booking $booking)
    {
        $booking->update(['status' => 'completed']);
        $this->notifyCustomer($booking, 'Booking Completed', 'Hi '.$booking->customer->name.',', 'Your booking #'.$booking->id.' has been marked as completed. Please leave a review!', route('customer.bookings.show', $booking));
        if ($request->ajax()) return response()->json(['success' => true]);
        return redirect()->back()->with('success', 'Booking completed!');
    }

    public function cancel(Request $request, Booking $booking)
    {
        $refundInfo = $this->cancellationService->cancel($booking);
        $this->notifyCustomer($booking, 'Booking Cancelled', 'Hi '.$booking->customer->name.',', 'Your booking #'.$booking->id.' has been cancelled. Refund: PKR '.number_format($refundInfo['refund_amount']).'.', route('customer.bookings.show', $booking));
        if ($request->ajax()) {
            return response()->json(['success' => true, 'refund' => $refundInfo]);
        }
        return redirect()->back()->with('success', 'Booking cancelled! Refund: PKR '.number_format($refundInfo['refund_amount']));
    }

    public function recordPayment(Request $request, Booking $booking)
    {
        $request->validate([
            'type' => 'required|in:advance,balance,refund',
            'amount' => 'required|numeric|min:0',
            'method' => 'required|string',
        ]);

        $payment = Payment::create([
            'booking_id' => $booking->id,
            'type' => $request->type,
            'amount' => $request->amount,
            'method' => $request->method,
            'status' => 'received',
            'received_by' => auth()->id(),
        ]);

        if ($request->ajax()) return response()->json(['success' => true, 'payment' => $payment]);
        return redirect()->back()->with('success', 'Payment recorded!');
    }

    private function notifyCustomer(Booking $booking, string $subject, string $greeting, string $body, ?string $actionUrl = null): void
    {
        try {
            Mail::to($booking->customer->email)->send(new BookingStatusMail(
                $booking, $subject, $greeting, $body, $actionUrl
            ));
        } catch (\Exception $e) {
            \Log::warning('Failed to send booking notification: '.$e->getMessage());
        }
    }
}
