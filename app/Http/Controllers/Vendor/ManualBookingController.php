<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\AvailabilitySlot;
use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\HallUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ManualBookingController extends Controller
{
    public function store(Request $request)
    {
        $profile = auth()->user()->vendorProfile;
        if (! $profile) {
            abort(403);
        }

        $validated = $request->validate([
            'hall_unit_id' => 'required|exists:hall_units,id',
            'date' => 'required|date|after_or_equal:today',
            'slot_type' => 'required|in:all_day,noon,evening',
            'event_type' => 'required|in:wedding,engagement,corporate,birthday,home,other',
            'client_name' => 'required|string|max:255',
            'client_phone' => 'nullable|string|max:20',
            'amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        $unit = HallUnit::where('id', $validated['hall_unit_id'])
            ->whereHas('hall', fn ($q) => $q->where('vendor_profile_id', $profile->id))
            ->first();

        if (! $unit) {
            return $this->error($request, 'This hall unit does not belong to your account.', 403);
        }

        $slotType = $validated['slot_type'] === 'all_day' ? null : $validated['slot_type'];

        $conflict = AvailabilitySlot::where('resource_type', 'App\Models\HallUnit')
            ->where('resource_id', $unit->id)
            ->whereDate('date', $validated['date'])
            ->where(function ($q) use ($slotType) {
                $q->where('slot_type', $slotType)->orWhereNull('slot_type');
            })
            ->where(function ($q) {
                $q->whereIn('status', ['booked', 'blocked_offline'])
                    ->orWhere(function ($held) {
                        $held->where('status', 'held')
                            ->where(function ($h) {
                                $h->whereNull('held_until')->orWhere('held_until', '>', now());
                            });
                    });
            })
            ->lockForUpdate()
            ->first();

        if ($conflict) {
            return $this->error($request, 'This unit is already booked or blocked on the selected date and slot.', 409);
        }

        $notes = collect([
            'Manual booking',
            $validated['client_name'] ? 'Client: '.$validated['client_name'] : null,
            $validated['client_phone'] ? 'Phone: '.$validated['client_phone'] : null,
            $validated['notes'] ?? null,
        ])->filter()->implode("\n");

        $booking = DB::transaction(function () use ($profile, $unit, $validated, $slotType, $notes) {
            $booking = Booking::create([
                'customer_id' => null,
                'booking_type' => 'manual',
                'event_date' => $validated['date'],
                'time_slot' => $slotType,
                'event_type' => $validated['event_type'],
                'status' => 'verified',
                'total_price' => $validated['amount'] ?? 0,
                'notes' => $notes,
            ]);

            BookingItem::create([
                'booking_id' => $booking->id,
                'itemable_type' => 'App\Models\HallUnit',
                'itemable_id' => $unit->id,
                'vendor_profile_id' => $profile->id,
                'price' => $validated['amount'] ?? 0,
                'time_slot' => $slotType,
                'vendor_status' => 'accepted',
            ]);

            AvailabilitySlot::create([
                'resource_type' => 'App\Models\HallUnit',
                'resource_id' => $unit->id,
                'date' => $validated['date'],
                'slot_type' => $slotType,
                'time_slot' => null,
                'status' => 'booked',
                'booking_id' => $booking->id,
            ]);

            return $booking;
        });

        if ($request->ajax()) {
            return response()->json(['success' => true, 'booking_id' => $booking->id]);
        }

        return redirect()->back()->with('success', 'Manual booking '.$booking->reference.' added.');
    }

    public function destroy(Request $request, Booking $booking)
    {
        $profile = auth()->user()->vendorProfile;
        if (! $profile || $booking->booking_type !== 'manual') {
            abort(403);
        }

        $ownsItem = BookingItem::where('booking_id', $booking->id)
            ->where('vendor_profile_id', $profile->id)
            ->exists();

        if (! $ownsItem) {
            abort(403);
        }

        DB::transaction(function () use ($booking) {
            AvailabilitySlot::where('booking_id', $booking->id)
                ->whereIn('status', ['booked'])
                ->update(['status' => 'available', 'booking_id' => null, 'held_until' => null]);
            $booking->update(['status' => 'cancelled']);
        });

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Manual booking cancelled and slot released.');
    }

    protected function error(Request $request, string $message, int $status = 422)
    {
        if ($request->ajax()) {
            return response()->json(['success' => false, 'message' => $message], $status);
        }

        return redirect()->back()->with('error', $message);
    }
}
