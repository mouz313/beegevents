<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\Request;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;

class WebhookController extends Controller
{
    public function handleStripe(Request $request)
    {
        $secret = config('services.stripe.webhook.secret');

        if (! $secret) {
            return response()->json(['error' => 'Stripe webhook not configured.'], 503);
        }

        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');

        try {
            $event = Webhook::constructEvent($payload, $signature, $secret);
        } catch (SignatureVerificationException $e) {
            return response()->json(['error' => 'Invalid signature.'], 400);
        }

        $intent = $event->data->object ?? null;

        switch ($event->type) {
            case 'payment_intent.succeeded':
                if ($intent && $intent->metadata && $intent->metadata['booking_id']) {
                    $this->recordSuccessfulIntent($intent);
                }
                break;

            case 'payment_intent.payment_failed':
                if ($intent && $intent->metadata && $intent->metadata['booking_id']) {
                    $this->recordFailedIntent($intent);
                }
                break;
        }

        return response()->json(['received' => true]);
    }

    protected function recordSuccessfulIntent($intent): void
    {
        $booking = Booking::find($intent->metadata['booking_id']);

        if (! $booking) {
            return;
        }

        $exists = $booking->payments()
            ->where('transaction_id', $intent->id)
            ->exists();

        if ($exists) {
            return;
        }

        Payment::create([
            'booking_id' => $booking->id,
            'type' => $intent->metadata['payment_type'] ?? 'advance',
            'amount' => $intent->amount / 100,
            'method' => 'stripe',
            'status' => 'received',
            'transaction_id' => $intent->id,
            'gateway_response' => json_encode(['intent_status' => $intent->status]),
        ]);
    }

    protected function recordFailedIntent($intent): void
    {
        $booking = Booking::find($intent->metadata['booking_id'] ?? null);

        if (! $booking) {
            return;
        }

        $exists = $booking->payments()
            ->where('transaction_id', $intent->id)
            ->exists();

        if ($exists) {
            return;
        }

        Payment::create([
            'booking_id' => $booking->id,
            'type' => $intent->metadata['payment_type'] ?? 'advance',
            'amount' => $intent->amount / 100,
            'method' => 'stripe',
            'status' => 'failed',
            'transaction_id' => $intent->id,
            'gateway_response' => json_encode(['intent_status' => $intent->status]),
        ]);
    }
}
