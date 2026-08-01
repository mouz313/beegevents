<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\BookingStatusMail;
use App\Models\AvailabilitySlot;
use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\HallUnit;
use App\Models\Package;
use App\Models\Payment;
use App\Models\ServiceListing;
use App\Services\BookingPdfService;
use App\Services\CancellationService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        $packages = Package::withCount('packageItems')->get();
        $hallUnits = HallUnit::with('hall')->get();
        $listings = ServiceListing::with('serviceCategory', 'vendorProfile')->get();

        return view('admin.bookings.show', compact('booking', 'packages', 'hallUnits', 'listings'));
    }

    public function download(Booking $booking)
    {
        $booking->load('customer', 'bookingItems.itemable', 'bookingItems.vendorProfile', 'payments');

        return app(BookingPdfService::class)->download($booking);
    }

    public function invoice(Booking $booking)
    {
        $booking->load('customer', 'bookingItems.itemable', 'bookingItems.vendorProfile', 'payments');

        return app(BookingPdfService::class)->downloadInvoice($booking);
    }

    public function verify(Request $request, Booking $booking)
    {
        $booking->update(['status' => 'verified']);
        $this->notifyCustomer($booking, 'Booking Verified', 'Hi '.$booking->customer->name.',', 'Your booking #'.$booking->id.' has been verified. We will confirm shortly.', route('customer.bookings.show', $booking));
        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Booking verified!');
    }

    public function confirm(Request $request, Booking $booking)
    {
        $booking->update(['status' => 'confirmed']);
        $this->notifyCustomer($booking, 'Booking Confirmed', 'Hi '.$booking->customer->name.',', 'Your booking #'.$booking->id.' has been confirmed! We look forward to serving you.', route('customer.bookings.show', $booking));
        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Booking confirmed!');
    }

    public function complete(Request $request, Booking $booking)
    {
        $booking->update(['status' => 'completed']);
        $this->notifyCustomer($booking, 'Booking Completed', 'Hi '.$booking->customer->name.',', 'Your booking #'.$booking->id.' has been marked as completed. Please leave a review!', route('customer.bookings.show', $booking));
        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

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

        if ($request->ajax()) {
            return response()->json(['success' => true, 'payment' => $payment]);
        }

        return redirect()->back()->with('success', 'Payment recorded!');
    }

    public function verifyPayment(Request $request, Payment $payment)
    {
        if ($payment->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Payment is not pending.'], 422);
        }

        $payment->update(['status' => 'received', 'received_by' => auth()->id()]);

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Payment verified!');
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        $request->validate(['status' => 'required|string']);

        $current = $booking->status;
        $target = $request->status;

        if ($current === 'cancelled' || $current === 'completed') {
            return $this->jsonResponse($request, ['success' => false, 'message' => 'This booking cannot change status.'], 422);
        }

        if ($target === 'cancelled') {
            $refundInfo = $this->cancellationService->cancel($booking);
            $this->notifyCustomer($booking, 'Booking Cancelled', 'Hi '.$booking->customer->name.',', 'Your booking #'.$booking->id.' has been cancelled. Refund: PKR '.number_format($refundInfo['refund_amount']).'.', route('customer.bookings.show', $booking));

            return $request->ajax()
                ? response()->json(['success' => true, 'refund' => $refundInfo])
                : redirect()->back()->with('success', 'Booking cancelled! Refund: PKR '.number_format($refundInfo['refund_amount']));
        }

        $allowed = [
            'requested' => ['verified', 'discussing'],
            'discussing' => ['confirmed'],
            'verified' => ['confirmed', 'discussing'],
            'confirmed' => ['completed'],
        ];

        if (! in_array($target, $allowed[$current] ?? [])) {
            return $this->jsonResponse($request, ['success' => false, 'message' => "Cannot change status from '$current' to '$target'."], 422);
        }

        $booking->update(['status' => $target]);
        $this->notifyCustomer($booking, 'Booking '.ucfirst($target), 'Hi '.$booking->customer->name.',', 'Your booking #'.$booking->id.' is now '.$target.'.', route('customer.bookings.show', $booking));

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Booking status updated!');
    }

    public function updatePrice(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'negotiated_price' => 'nullable|numeric|min:0',
            'price_negotiation_note' => 'nullable|string|max:1000',
        ]);

        $agreed = $validated['negotiated_price'] !== null && $validated['negotiated_price'] !== ''
            ? (float) $validated['negotiated_price']
            : null;

        $commissionRate = config('commission.types.'.$booking->booking_type, config('commission.default_rate', 10));
        $commissionAmount = $agreed !== null ? round($agreed * ($commissionRate / 100), 2) : $booking->commission_amount;

        $booking->update([
            'negotiated_price' => $agreed,
            'price_negotiation_note' => $validated['price_negotiation_note'] ?? null,
            'commission_amount' => $commissionAmount,
            'price_offer' => null,
            'price_offer_status' => null,
            'price_offer_note' => null,
            'price_offer_sent_at' => null,
        ]);

        $this->notifyCustomer($booking, 'Booking Price Updated', 'Hi '.$booking->customer->name.',', 'Your booking #'.$booking->id.' agreed price is now PKR '.number_format($booking->price()).'.', route('customer.bookings.show', $booking));

        if ($request->ajax()) {
            return response()->json(['success' => true, 'price' => $booking->price()]);
        }

        return redirect()->back()->with('success', 'Agreed price updated!');
    }

    public function sendPriceOffer(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'price_offer' => 'required|numeric|min:0',
            'price_offer_note' => 'nullable|string|max:1000',
        ]);

        $booking->update([
            'price_offer' => $validated['price_offer'],
            'price_offer_status' => 'pending',
            'price_offer_note' => $validated['price_offer_note'] ?? null,
            'price_offer_sent_at' => now(),
            'negotiated_price' => null,
        ]);

        $this->notifyCustomer($booking, 'New Price Offer #'.$booking->id, 'Hi '.$booking->customer->name.',', 'We have proposed a new price of PKR '.number_format($validated['price_offer']).' for your booking #'.$booking->id.'. Please review it in your dashboard.', route('customer.bookings.show', $booking));

        app(NotificationService::class)->notifyParticipants(
            $booking,
            auth()->id(),
            'New price offer PKR '.number_format($validated['price_offer']),
            'Booking #'.$booking->id.' — please review the new proposed price.'
        );

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Price offer sent to customer!');
    }

    public function changePackage(Request $request, Booking $booking)
    {
        if ($booking->status === 'completed' || $booking->status === 'cancelled') {
            return $this->jsonResponse($request, ['success' => false, 'message' => 'Cannot change the package on a '.$booking->status.' booking.'], 422);
        }

        $request->validate(['package_id' => 'required|exists:packages,id']);
        $package = Package::with('packageItems')->findOrFail($request->package_id);

        try {
            DB::transaction(function () use ($booking, $package) {
                foreach ($package->packageItems as $packageItem) {
                    $itemable = $packageItem->itemable;
                    if (! $itemable || $packageItem->itemable_type !== 'App\Models\HallUnit') {
                        continue;
                    }

                    $conflict = $this->hallConflict($itemable->id, $booking->event_date);
                    if ($conflict) {
                        throw new \RuntimeException($itemable->unit_name.' is not available on '.$booking->event_date->format('d M Y').'.');
                    }
                }

                AvailabilitySlot::where('booking_id', $booking->id)
                    ->whereIn('status', ['held', 'booked'])
                    ->delete();

                $booking->bookingItems()->delete();

                $commissionRate = config('commission.types.package', config('commission.default_rate', 10));
                $commissionAmount = round($package->total_price * ($commissionRate / 100), 2);

                $booking->update([
                    'booking_type' => 'package',
                    'total_price' => $package->total_price,
                    'negotiated_price' => null,
                    'price_offer' => null,
                    'price_offer_status' => null,
                    'price_offer_note' => null,
                    'price_offer_sent_at' => null,
                    'commission_amount' => $commissionAmount,
                    'notes' => 'Package: '.$package->title,
                ]);

                $heldUntil = now()->addHours(24);

                foreach ($package->packageItems as $packageItem) {
                    $itemable = $packageItem->itemable;
                    if (! $itemable) {
                        continue;
                    }

                    BookingItem::create([
                        'booking_id' => $booking->id,
                        'itemable_type' => $packageItem->itemable_type,
                        'itemable_id' => $packageItem->itemable_id,
                        'vendor_profile_id' => $itemable->vendor_profile_id ?? $itemable->hall?->vendor_profile_id,
                        'price' => $itemable->price ?? $itemable->base_price ?? 0,
                    ]);

                    if ($packageItem->itemable_type === 'App\Models\HallUnit') {
                        AvailabilitySlot::create([
                            'resource_type' => 'App\Models\HallUnit',
                            'resource_id' => $itemable->id,
                            'date' => $booking->event_date,
                            'status' => 'held',
                            'booking_id' => $booking->id,
                            'held_until' => $heldUntil,
                        ]);
                    }
                }
            });
        } catch (\RuntimeException $e) {
            return $this->jsonResponse($request, ['success' => false, 'message' => $e->getMessage()], 409);
        }

        $this->notifyCustomer($booking, 'Package Updated #'.$booking->id, 'Hi '.$booking->customer->name.',', 'Your booking #'.$booking->id.' is now on the '.$package->title.' (PKR '.number_format($package->total_price).').', route('customer.bookings.show', $booking));

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Package changed to '.$package->title.'!');
    }

    public function addItem(Request $request, Booking $booking)
    {
        if ($booking->status === 'completed' || $booking->status === 'cancelled') {
            return $this->jsonResponse($request, ['success' => false, 'message' => 'Cannot modify a '.$booking->status.' booking.'], 422);
        }

        $validated = $request->validate([
            'type' => 'required|in:hall_unit,service_listing',
            'itemable_id' => 'required|integer',
        ]);

        $itemableType = $validated['type'] === 'hall_unit' ? 'App\Models\HallUnit' : 'App\Models\ServiceListing';
        $itemable = $validated['type'] === 'hall_unit'
            ? HallUnit::find($validated['itemable_id'])
            : ServiceListing::find($validated['itemable_id']);

        if (! $itemable) {
            return $this->jsonResponse($request, ['success' => false, 'message' => 'Item not found.'], 422);
        }

        if ($validated['type'] === 'hall_unit') {
            $conflict = $this->hallConflict($itemable->id, $booking->event_date);
            if ($conflict) {
                return $this->jsonResponse($request, ['success' => false, 'message' => $itemable->unit_name.' is not available on '.$booking->event_date->format('d M Y').'.'], 409);
            }
        }

        $item = BookingItem::firstOrCreate(
            [
                'booking_id' => $booking->id,
                'itemable_type' => $itemableType,
                'itemable_id' => $itemable->id,
            ],
            [
                'vendor_profile_id' => $itemable->vendor_profile_id ?? $itemable->hall?->vendor_profile_id,
                'price' => $itemable->price ?? $itemable->base_price ?? 0,
            ]
        );

        if ($validated['type'] === 'hall_unit') {
            $slotExists = AvailabilitySlot::where('resource_type', 'App\Models\HallUnit')
                ->where('resource_id', $itemable->id)
                ->where('date', $booking->event_date)
                ->exists();

            if (! $slotExists) {
                AvailabilitySlot::create([
                    'resource_type' => 'App\Models\HallUnit',
                    'resource_id' => $itemable->id,
                    'date' => $booking->event_date,
                    'status' => 'held',
                    'booking_id' => $booking->id,
                    'held_until' => now()->addHours(24),
                ]);
            }
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'item' => [
                    'id' => $item->id,
                    'type' => str_replace('_', ' ', class_basename($item->itemable_type)),
                    'vendor' => $item->vendorProfile->business_name ?? 'N/A',
                    'price' => (float) $item->price,
                ],
            ]);
        }

        return redirect()->back()->with('success', 'Item added!');
    }

    public function removeItem(Request $request, Booking $booking, BookingItem $bookingItem)
    {
        if ($bookingItem->booking_id !== $booking->id) {
            abort(403);
        }

        if ($bookingItem->itemable_type === 'App\Models\HallUnit') {
            AvailabilitySlot::where('booking_id', $booking->id)
                ->where('resource_type', 'App\Models\HallUnit')
                ->where('resource_id', $bookingItem->itemable_id)
                ->where('date', $booking->event_date)
                ->delete();
        }

        $bookingItem->delete();

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Item removed!');
    }

    protected function hallConflict(int $hallUnitId, $date)
    {
        return AvailabilitySlot::where('resource_type', 'App\Models\HallUnit')
            ->where('resource_id', $hallUnitId)
            ->where('date', $date)
            ->lockForUpdate()
            ->where(function ($q) {
                $q->whereIn('status', ['booked', 'blocked_offline'])
                    ->orWhere(function ($held) {
                        $held->where('status', 'held')
                            ->where(function ($h) {
                                $h->whereNull('held_until')
                                    ->orWhere('held_until', '>', now());
                            });
                    });
            })
            ->first();
    }

    protected function jsonResponse(Request $request, array $payload, int $status = 200)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($payload, $status);
        }

        return redirect()->back()->withErrors(['action' => $payload['message'] ?? 'Action failed.']);
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
