<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Mail\BookingStatusMail;
use App\Models\AvailabilitySlot;
use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\Package;
use App\Services\CancellationService;
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
        ]);

        $totalPrice = collect($cart)->sum('price');
        $bookingType = count($cart) > 1 ? 'multi' : 'single';
        $commissionRate = config("commission.types.$bookingType", config('commission.default_rate', 10));
        $commissionAmount = round($totalPrice * ($commissionRate / 100), 2);

        try {
            $booking = DB::transaction(function () use ($cart, $validated, $totalPrice, $bookingType, $commissionAmount) {
                foreach ($cart as $item) {
                    if ($item['type'] !== 'hall_unit') {
                        continue;
                    }

                    $conflict = AvailabilitySlot::where('resource_type', 'App\Models\HallUnit')
                        ->where('resource_id', $item['id'])
                        ->where('date', $validated['event_date'])
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
                            $item['name'].' is not available on '.$validated['event_date'].'.'
                        );
                    }
                }

                $booking = Booking::create([
                    'customer_id' => auth()->id(),
                    'booking_type' => $bookingType,
                    'event_date' => $validated['event_date'],
                    'event_type' => $validated['event_type'],
                    'total_price' => $totalPrice,
                    'commission_amount' => $commissionAmount,
                    'notes' => $validated['notes'] ?? null,
                ]);

                $heldUntil = now()->addHours(24);

                foreach ($cart as $item) {
                    $itemableType = $item['type'] === 'hall_unit' ? 'App\Models\HallUnit' : 'App\Models\ServiceListing';

                    BookingItem::create([
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
                            'date' => $validated['event_date'],
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

    public function bookPackage(Request $request, Package $package)
    {
        $validated = $request->validate([
            'event_date' => 'required|date|after:today',
            'event_type' => 'required|in:wedding,engagement,corporate,birthday,home,other',
            'notes' => 'nullable|string|max:1000',
        ]);

        $commissionRate = config('commission.types.package', config('commission.default_rate', 10));
        $commissionAmount = round($package->total_price * ($commissionRate / 100), 2);

        try {
            $booking = DB::transaction(function () use ($package, $validated, $commissionAmount) {
                foreach ($package->packageItems as $packageItem) {
                    $itemable = $packageItem->itemable;
                    if (! $itemable || $packageItem->itemable_type !== 'App\Models\HallUnit') {
                        continue;
                    }

                    $conflict = AvailabilitySlot::where('resource_type', 'App\Models\HallUnit')
                        ->where('resource_id', $itemable->id)
                        ->where('date', $validated['event_date'])
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
                            $itemable->unit_name.' is not available on '.$validated['event_date'].'.'
                        );
                    }
                }

                $booking = Booking::create([
                    'customer_id' => auth()->id(),
                    'booking_type' => 'package',
                    'event_date' => $validated['event_date'],
                    'event_type' => $validated['event_type'],
                    'total_price' => $package->total_price,
                    'commission_amount' => $commissionAmount,
                    'notes' => ($validated['notes'] ?? null) ?: 'Package: '.$package->title,
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
                            'date' => $validated['event_date'],
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

        $this->sendBookingNotifications($booking);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'booking_id' => $booking->id]);
        }

        return redirect()->route('customer.bookings.show', $booking)->with('success', 'Package booked!');
    }

    protected function sendBookingNotifications(Booking $booking): void
    {
        try {
            Mail::to($booking->customer->email)->send(new BookingStatusMail(
                $booking,
                'Booking Request Received #'.$booking->id,
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
                    'New Booking Request #'.$booking->id,
                    'Hi '.$vendorProfile->user->name.',',
                    'You received a new booking request for '.$booking->event_date->format('d M Y').'. Please accept or decline it.',
                    route('vendor.bookings.index')
                ));
            } catch (\Exception $e) {
                \Log::warning('Vendor booking notification failed: '.$e->getMessage());
            }
        }
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
