<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService
    ) {}

    public function showPayment(Booking $booking)
    {
        if ($booking->customer_id !== auth()->id()) {
            abort(403);
        }

        $summary = $this->paymentService->getPaymentSummary($booking);
        $stripeKey = config('services.stripe.key');

        return view('customer.payments.pay', compact('booking', 'summary', 'stripeKey'));
    }

    public function createIntent(Request $request, Booking $booking)
    {
        if ($booking->customer_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'type' => 'required|in:advance,balance',
        ]);

        $type = $request->type;
        $amount = $type === 'advance'
            ? $this->paymentService->getAdvanceAmount($booking)
            : $this->paymentService->getBalanceAmount($booking);

        if ($amount <= 0) {
            return response()->json(['success' => false, 'message' => 'No payment required.'], 400);
        }

        try {
            $intent = $this->paymentService->createStripePaymentIntent($booking, $type, $amount);

            return response()->json([
                'success' => true,
                'client_secret' => $intent['client_secret'],
                'intent_id' => $intent['intent_id'],
                'amount' => $amount,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function confirmPayment(Request $request, Booking $booking)
    {
        if ($booking->customer_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'payment_intent_id' => 'required|string',
            'type' => 'required|in:advance,balance',
        ]);

        try {
            $result = $this->paymentService->confirmStripePayment($request->payment_intent_id);

            $metadata = $result['metadata'] ?? [];
            if (($metadata['booking_id'] ?? null) != $booking->id) {
                return response()->json(['success' => false, 'message' => 'This payment does not belong to the booking.'], 403);
            }

            $expectedAmount = $request->type === 'advance'
                ? $this->paymentService->getAdvanceAmount($booking)
                : $this->paymentService->getBalanceAmount($booking);

            if (round((float) $result['amount'], 2) !== round($expectedAmount, 2)) {
                return response()->json(['success' => false, 'message' => 'Payment amount does not match the booking.'], 422);
            }

            if ($result['status'] === 'succeeded') {
                $this->paymentService->recordPayment(
                    $booking,
                    $request->type,
                    $result['amount'],
                    'stripe',
                    'received',
                    $request->payment_intent_id
                );

                return response()->json(['success' => true]);
            }

            return response()->json(['success' => false, 'message' => 'Payment not completed.'], 400);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function manualPayment(Request $request, Booking $booking)
    {
        if ($booking->customer_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'type' => 'required|in:advance,balance',
            'amount' => 'required|numeric|min:1',
            'proof' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $proofPath = null;
        if ($request->hasFile('proof')) {
            $proofPath = $request->file('proof')->store('payment-proofs', 'public');
        }

        $this->paymentService->recordPayment(
            $booking,
            $request->type,
            $request->amount,
            'bank_transfer',
            'pending',
            null,
            null,
            $proofPath
        );

        return redirect()->back()->with('success', 'Payment instruction recorded. Admin will verify after receipt.');
    }
}
