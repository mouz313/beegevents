<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\VendorProfile;
use App\Models\AvailabilitySlot;
use Illuminate\Support\Facades\DB;
use Stripe\StripeClient;

class CancellationService
{
    protected ?StripeClient $stripe = null;
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
        $secret = config('services.stripe.secret');
        if ($secret) {
            $this->stripe = new StripeClient($secret);
        }
    }

    public function calculateRefund(Booking $booking): array
    {
        $totalPaid = $booking->payments()
            ->whereIn('status', ['received'])
            ->sum('amount');

        if ($totalPaid <= 0) {
            return [
                'refund_amount' => 0,
                'cancellation_fee' => 0,
                'policy_description' => 'No payment made — nothing to refund.',
            ];
        }

        $vendorProfiles = VendorProfile::whereIn('id', $booking->bookingItems()->pluck('vendor_profile_id'))->get();

        $minFreeDays = PHP_INT_MAX;
        $minRefundPercent = 100;
        $descriptions = [];

        foreach ($vendorProfiles as $vp) {
            if ($vp->cancel_free_days !== null) {
                $minFreeDays = min($minFreeDays, $vp->cancel_free_days);
            }
            if ($vp->cancel_refund_percent !== null) {
                $minRefundPercent = min($minRefundPercent, $vp->cancel_refund_percent);
            }
            if ($vp->cancellation_policy) {
                $descriptions[] = $vp->business_name . ': ' . $vp->cancellation_policy;
            }
        }

        $daysUntilEvent = now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($booking->event_date)->startOfDay(), false);

        if ($minFreeDays === PHP_INT_MAX) {
            return [
                'refund_amount' => 0,
                'cancellation_fee' => $totalPaid,
                'policy_description' => 'No cancellation policy set by vendor(s). No refund applicable.',
                'days_until_event' => $daysUntilEvent,
                'vendor_policies' => $descriptions,
            ];
        }

        if ($daysUntilEvent >= $minFreeDays) {
            return [
                'refund_amount' => $totalPaid,
                'cancellation_fee' => 0,
                'policy_description' => 'Free cancellation — full refund.',
                'days_until_event' => $daysUntilEvent,
                'vendor_policies' => $descriptions,
            ];
        }

        $refundAmount = $totalPaid * ($minRefundPercent / 100);

        return [
            'refund_amount' => round($refundAmount, 2),
            'cancellation_fee' => round($totalPaid - $refundAmount, 2),
            'policy_description' => "Cancelled {$daysUntilEvent} day(s) before event. Refund: {$minRefundPercent}%.",
            'days_until_event' => $daysUntilEvent,
            'vendor_policies' => $descriptions,
        ];
    }

    public function cancel(Booking $booking): array
    {
        $refundInfo = $this->calculateRefund($booking);

        DB::transaction(function () use ($booking, $refundInfo) {
            $booking->update(['status' => 'cancelled']);

            AvailabilitySlot::where('booking_id', $booking->id)
                ->whereIn('status', ['held', 'booked'])
                ->delete();

            if ($refundInfo['refund_amount'] > 0) {
                $stripePayments = $booking->payments()
                    ->where('method', 'stripe')
                    ->where('status', 'received')
                    ->whereNotNull('transaction_id')
                    ->get();

                foreach ($stripePayments as $payment) {
                    try {
                        if ($this->stripe) {
                            $this->stripe->refunds->create([
                                'payment_intent' => $payment->transaction_id,
                                'amount' => (int) round(min($refundInfo['refund_amount'], $payment->amount) * 100),
                            ]);
                        }
                    } catch (\Exception $e) {
                        \Log::warning('Stripe refund failed for payment #'.$payment->id.': '.$e->getMessage());
                    }
                }

                $this->paymentService->recordPayment(
                    $booking,
                    'refund',
                    $refundInfo['refund_amount'],
                    'stripe',
                    'refunded',
                    'refund_'.$booking->id,
                    ['cancellation_fee' => $refundInfo['cancellation_fee']]
                );
            }
        });

        return $refundInfo;
    }
}
