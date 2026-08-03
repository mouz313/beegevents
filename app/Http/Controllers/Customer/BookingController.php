<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Mail\BookingStatusMail;
use App\Models\AvailabilitySlot;
use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\VendorCombo;
use App\Services\CancellationService;
use App\Services\NotificationService;
use App\Services\BookingPdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        $validated = $request->validate([
            'event_date' => 'required|date|after:today',
            'event_type' => 'required|in:wedding,engagement,corporate,birthday,home,other',
            'notes' => 'nullable|string|max:1000',
            'agreement_accepted' => 'required|accepted',
        ]);

        $totalPrice = collect($cart)->sum(function ($item) {
            return ($item['price'] ?? 0) + ($item['extras_total'] ?? 0) + ($item['menu_set_price'] ?? 0);
        });
        $bookingType = count($cart) > 1 ? 'multi' : 'single';

        try {
            $booking = DB::transaction(function () use ($cart, $validated, $totalPrice, $bookingType) {
                foreach ($cart as $item) {
                    if ($item['type'] !== 'hall_unit') {
                        continue;
                    }

                    $conflict = AvailabilitySlot::where('resource_type', 'App\Models\HallUnit')
                        ->where('resource_id', $item['id'])
                        ->where('date', $item['date'])
                        ->where(function ($q) use ($item) {
                            $q->where('slot_type', $item['time_slot'] ?? 'noon')->orWhereNull('slot_type');
                        })
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

                    if ($conflict) {
                        throw new \RuntimeException(
                            $item['name'].' is not available for '.ucfirst($item['time_slot'] ?? 'noon').' on '.\Carbon\Carbon::parse($item['date'])->format('d M Y').'.'
                        );
                    }
                }

                // Booking date derived from the first hall-unit cart item (fall back to validated input)
                $eventDate = collect($cart)->firstWhere('type', 'hall_unit')['date'] ?? $validated['event_date'];
                $firstHallSlot = collect($cart)->firstWhere('type', 'hall_unit')['time_slot'] ?? null;

                $booking = Booking::create([
                    'customer_id' => auth()->id(),
                    'booking_type' => $bookingType,
                    'event_date' => $eventDate,
                    'time_slot' => $firstHallSlot,
                    'event_type' => $validated['event_type'],
                    'total_price' => $totalPrice,
                    'notes' => $validated['notes'] ?? null,
                    'agreement_accepted_at' => now(),
                ]);

                $heldUntil = now()->addHours(24);

                foreach ($cart as $item) {
                    $itemableType = $item['type'] === 'hall_unit' ? 'App\Models\HallUnit' : 'App\Models\ServiceListing';

                    BookingItem::create([
                        'booking_id' => $booking->id,
                        'itemable_type' => $itemableType,
                        'itemable_id' => $item['id'],
                        'vendor_profile_id' => $item['vendor_id'],
                        'price' => ($item['price'] ?? 0) + ($item['extras_total'] ?? 0) + ($item['menu_set_price'] ?? 0),
                        'time_slot' => $item['time_slot'] ?? null,
                        'extras' => $item['extras'] ?? [],
                        'menu_set_id' => $item['menu_set_id'] ?? null,
                        'guests' => $item['guests'] ?? null,
                        'catering_mode' => $item['catering_mode'] ?? null,
                    ]);

                    if ($item['type'] === 'hall_unit') {
                        AvailabilitySlot::create([
                            'resource_type' => 'App\Models\HallUnit',
                            'resource_id' => $item['id'],
                            'date' => $item['date'],
                            'slot_type' => $item['time_slot'] ?? 'noon',
                            'status' => 'held',
                            'booking_id' => $booking->id,
                            'held_until' => $heldUntil,
                        ]);
                    }
                }

                return $booking;
            });
        } catch (\RuntimeException $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 409);
            }

            return redirect()->back()->withErrors(['event_date' => $e->getMessage()])->withInput();
        }

        session()->forget('cart');

        $this->sendBookingNotifications($booking);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'booking_id' => $booking->id]);
        }

        return redirect()->route('customer.bookings.show', $booking)->with('success', 'Booking created!');
    }

    public function comboShow(VendorCombo $combo)
    {
        $combo->load('package', 'vendorProfile.user', 'items.itemable');

        $vendor = $combo->vendorProfile;

        if (! $combo->is_active
            || ! $combo->isFullyFilled()
            || ! $vendor
            || ! $vendor->visibleOnSite()
            || ($combo->packagePurchase && $combo->packagePurchase->status !== 'active')) {
            abort(404);
        }

        return view('browse.combo-book', compact('combo', 'vendor'));
    }

    public function bookCombo(Request $request, VendorCombo $combo)
    {
        $combo->load('package', 'items.itemable');

        $validated = $request->validate([
            'event_date' => 'required|date|after:today',
            'event_type' => 'required|in:wedding,engagement,corporate,birthday,home,other',
            'time_slot' => 'nullable|in:noon,evening',
            'notes' => 'nullable|string|max:1000',
            'agreement_accepted' => 'required|accepted',
        ]);

        $vendor = $combo->vendorProfile;

        if (! $combo->is_active
            || ! $combo->isFullyFilled()
            || ! $vendor
            || ! $vendor->visibleOnSite()
            || ($combo->packagePurchase && $combo->packagePurchase->status !== 'active')) {
            return redirect()->back()->with('error', 'This combo is no longer available.');
        }

        $timeSlot = $validated['time_slot'] ?? 'noon';

        try {
            $booking = DB::transaction(function () use ($combo, $validated, $timeSlot) {
                foreach ($combo->items as $comboItem) {
                    if ($comboItem->itemable_type !== 'App\Models\HallUnit') {
                        continue;
                    }

                    $itemable = $comboItem->itemable;
                    if (! $itemable) {
                        continue;
                    }

                    $conflict = AvailabilitySlot::where('resource_type', 'App\Models\HallUnit')
                        ->where('resource_id', $itemable->id)
                        ->where('date', $validated['event_date'])
                        ->where(function ($q) use ($timeSlot) {
                            $q->where('slot_type', $timeSlot)->orWhereNull('slot_type');
                        })
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

                    if ($conflict) {
                        throw new \RuntimeException(
                            ($itemable->unit_name ?? 'Hall unit').' is not available for '.ucfirst($timeSlot).' on '.\Carbon\Carbon::parse($validated['event_date'])->format('d M Y').'.'
                        );
                    }
                }

                $booking = Booking::create([
                    'customer_id' => auth()->id(),
                    'booking_type' => 'multi',
                    'event_date' => $validated['event_date'],
                    'time_slot' => $timeSlot,
                    'event_type' => $validated['event_type'],
                    'total_price' => $combo->total_price,
                    'notes' => ($validated['notes'] ?? null) ?: 'Combo: '.($combo->package->title ?? 'Combo'),
                    'agreement_accepted_at' => now(),
                ]);

                $heldUntil = now()->addHours(24);

                foreach ($combo->items as $comboItem) {
                    $itemable = $comboItem->itemable;
                    if (! $itemable) {
                        continue;
                    }

                    BookingItem::create([
                        'booking_id' => $booking->id,
                        'itemable_type' => $comboItem->itemable_type,
                        'itemable_id' => $comboItem->itemable_id,
                        'vendor_profile_id' => $comboItem->itemable_type === 'App\Models\HallUnit'
                            ? $itemable->hall?->vendor_profile_id
                            : $itemable->vendor_profile_id,
                        'price' => $itemable->price ?? $itemable->base_price ?? 0,
                        'time_slot' => $comboItem->itemable_type === 'App\Models\HallUnit' ? $timeSlot : null,
                    ]);

                    if ($comboItem->itemable_type === 'App\Models\HallUnit') {
                        AvailabilitySlot::create([
                            'resource_type' => 'App\Models\HallUnit',
                            'resource_id' => $itemable->id,
                            'date' => $validated['event_date'],
                            'slot_type' => $timeSlot,
                            'status' => 'held',
                            'booking_id' => $booking->id,
                            'held_until' => $heldUntil,
                        ]);
                    }
                }

                return $booking;
            });
        } catch (\RuntimeException $e) {
            return redirect()->back()->withErrors(['event_date' => $e->getMessage()])->withInput();
        }

        $this->sendBookingNotifications($booking);

        return redirect()->route('customer.bookings.show', $booking)->with('success', 'Combo booked!');
    }

    protected function sendBookingNotifications(Booking $booking): void
    {
        try {
            Mail::to($booking->customer->email)->send(new BookingStatusMail(
                $booking,
                'Booking Request Received '.$booking->reference,
                'Hi '.$booking->customer->name.',',
                'We have received your booking request for '.$booking->event_date->format('d M Y').'. You will be notified once vendors respond.',
                route('customer.bookings.show', $booking)
            ));
        } catch (\Exception $e) {
            \Log::warning('Customer booking notification failed: '.$e->getMessage());
        }

        $vendorProfiles = $booking->bookingItems()->with('vendorProfile.user')->get()
            ->pluck('vendorProfile')
            ->unique('id');

        foreach ($vendorProfiles as $vendorProfile) {
            try {
                Mail::to($vendorProfile->user->email)->send(new BookingStatusMail(
                    $booking,
                    'New Booking Request '.$booking->reference,
                    'Hi '.$vendorProfile->user->name.',',
                    'You received a new booking request for '.$booking->event_date->format('d M Y').'. Please accept or decline it.',
                    route('vendor.bookings.index')
                ));
            } catch (\Exception $e) {
                \Log::warning('Vendor booking notification failed: '.$e->getMessage());
            }
        }
    }

    public function acceptPriceOffer(Request $request, Booking $booking)
    {
        if ($booking->customer_id !== auth()->id()) {
            abort(403);
        }

        if (! $booking->hasPendingOffer()) {
            return $this->offerResponse($request, 'No pending price offer.', 422);
        }

        $booking->update([
            'negotiated_price' => $booking->price_offer,
            'price_offer' => null,
            'price_offer_status' => 'accepted',
            'price_offer_note' => null,
            'price_offer_sent_at' => now(),
        ]);

        app(NotificationService::class)->notifyParticipants(
            $booking,
            auth()->id(),
            'Price offer accepted',
            'Customer accepted the new price of PKR '.number_format($booking->price()).' for booking '.$booking->reference.'.'
        );

        if ($request->ajax()) {
            return response()->json(['success' => true, 'price' => $booking->price()]);
        }

        return redirect()->back()->with('success', 'Price offer accepted! Your new total is PKR '.number_format($booking->price()).'.');
    }

    public function declinePriceOffer(Request $request, Booking $booking)
    {
        if ($booking->customer_id !== auth()->id()) {
            abort(403);
        }

        if (! $booking->hasPendingOffer()) {
            return $this->offerResponse($request, 'No pending price offer.', 422);
        }

        $booking->update(['price_offer_status' => 'declined']);

        app(NotificationService::class)->notifyParticipants(
            $booking,
            auth()->id(),
            'Price offer declined',
            'Customer declined the price offer for booking '.$booking->reference.'.'
        );

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Price offer declined.');
    }

    protected function offerResponse(Request $request, string $message, int $status)
    {
        if ($request->ajax()) {
            return response()->json(['success' => false, 'message' => $message], $status);
        }

        return redirect()->back()->with('error', $message);
    }

    public function download(Booking $booking)
    {
        if ($booking->customer_id !== auth()->id()) {
            abort(403);
        }

        return app(BookingPdfService::class)->download($booking);
    }

    public function invoice(Booking $booking)
    {
        if ($booking->customer_id !== auth()->id()) {
            abort(403);
        }

        return app(BookingPdfService::class)->downloadInvoice($booking);
    }

    public function show(Booking $booking)
    {
        $booking->load('bookingItems.itemable', 'bookingItems.menuSet');        $refundInfo = null;
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

        if (! in_array($booking->status, ['requested', 'discussing', 'verified', 'confirmed'])) {
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
                'Your booking '.$booking->reference.' has been cancelled. Refund: PKR '.number_format($refundInfo['refund_amount']).'.',
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
