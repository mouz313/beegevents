<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Payment;
use Stripe\StripeClient;

class PaymentService
{
    protected ?StripeClient $stripe = null;

    public function __construct()
    {
        $secret = config('services.stripe.secret');
        if ($secret) {
            $this->stripe = new StripeClient($secret);
        }
    }

    public function createStripePaymentIntent(Booking $booking, string $type, float $amount): array
    {
        if (! $this->stripe) {
            throw new \RuntimeException('Stripe is not configured. Set STRIPE_SECRET in .env');
        }

        $intent = $this->stripe->paymentIntents->create([
            'amount' => (int) round($amount * 100),
            'currency' => 'pkr',
            'metadata' => [
                'booking_id' => $booking->id,
                'payment_type' => $type,
            ],
        ]);

        return [
            'client_secret' => $intent->client_secret,
            'intent_id' => $intent->id,
        ];
    }

    public function confirmStripePayment(string $paymentIntentId): array
    {
        if (! $this->stripe) {
            throw new \RuntimeException('Stripe is not configured.');
        }

        $intent = $this->stripe->paymentIntents->retrieve($paymentIntentId);

        return [
            'status' => $intent->status,
            'amount' => $intent->amount / 100,
            'metadata' => $intent->metadata->toArray(),
        ];
    }

    public function recordPayment(Booking $booking, string $type, float $amount, string $method = 'manual', string $status = 'received', ?string $transactionId = null, ?array $gatewayResponse = null, ?string $proofPath = null): Payment
    {
        return Payment::create([
            'booking_id' => $booking->id,
            'type' => $type,
            'amount' => $amount,
            'method' => $method,
            'status' => $status,
            'received_by' => auth()->id(),
            'transaction_id' => $transactionId,
            'proof_path' => $proofPath,
            'gateway_response' => $gatewayResponse ? json_encode($gatewayResponse) : null,
        ]);
    }

    public function getAdvanceAmount(Booking $booking): float
    {
        return $booking->total_price * 0.3;
    }

    public function getBalanceAmount(Booking $booking): float
    {
        $advancePaid = $booking->payments()
            ->where('type', 'advance')
            ->where('status', 'received')
            ->sum('amount');

        return max(0, $booking->total_price - $advancePaid);
    }

    public function isAdvancePaid(Booking $booking): bool
    {
        $advanceRequired = $this->getAdvanceAmount($booking);
        $advancePaid = $booking->payments()
            ->where('type', 'advance')
            ->where('status', 'received')
            ->sum('amount');

        return $advancePaid >= $advanceRequired;
    }

    public function isFullyPaid(Booking $booking): bool
    {
        $totalPaid = $booking->payments()
            ->whereIn('status', ['received'])
            ->sum('amount');

        return $totalPaid >= $booking->total_price;
    }

    public function getPaymentSummary(Booking $booking): array
    {
        $advancePaid = $booking->payments()
            ->where('type', 'advance')
            ->where('status', 'received')
            ->sum('amount');

        $balancePaid = $booking->payments()
            ->where('type', 'balance')
            ->where('status', 'received')
            ->sum('amount');

        return [
            'total' => $booking->total_price,
            'advance_required' => $this->getAdvanceAmount($booking),
            'advance_paid' => $advancePaid,
            'balance_paid' => $balancePaid,
            'remaining' => max(0, $booking->total_price - $advancePaid - $balancePaid),
            'is_advance_paid' => $this->isAdvancePaid($booking),
            'is_fully_paid' => $this->isFullyPaid($booking),
        ];
    }
}
