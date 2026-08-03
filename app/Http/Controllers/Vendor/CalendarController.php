<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\AvailabilitySlot;
use App\Models\HallUnit;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function index()
    {
        $profile = auth()->user()->vendorProfile;
        $hallUnits = collect();
        if ($profile && in_array($profile->vendor_type, ['hall', 'farmhouse'])) {
            $hallUnits = HallUnit::whereHas('hall', function($q) use ($profile) {
                $q->where('vendor_profile_id', $profile->id);
            })->get();
        }
        return view('vendor.calendar', compact('profile', 'hallUnits'));
    }

    public function getSlots(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'hall_unit_id' => 'nullable|exists:hall_units,id',
        ]);
        $profile = auth()->user()->vendorProfile;

        $query = AvailabilitySlot::where('resource_type', 'App\Models\HallUnit')
            ->whereIn('resource_id', HallUnit::whereHas('hall', function($q) use ($profile) {
                $q->where('vendor_profile_id', $profile->id);
            })->pluck('id'))
            ->whereDate('date', $request->date);

        if ($request->hall_unit_id) {
            $query->where('resource_id', $request->hall_unit_id);
        }

        $slots = $query->get();

        return response()->json(['slots' => $slots]);
    }

    public function blockSlot(Request $request)
    {
        $validated = $request->validate([
            'hall_unit_id' => 'required|exists:hall_units,id',
            'date' => 'required|date',
            'slot_type' => 'nullable|in:all_day,noon,evening',
            'time_slot' => 'nullable|integer|min:0|max:23',
            'notes' => 'nullable|string|max:500',
        ]);

        $slotType = $validated['slot_type'] ?? ($validated['time_slot'] !== null ? null : 'all_day');
        if ($slotType === 'all_day') {
            $slotType = null;
        }
        $timeSlot = $validated['time_slot'] ?? null;

        $slot = AvailabilitySlot::updateOrCreate(
            [
                'resource_type' => 'App\Models\HallUnit',
                'resource_id' => $request->hall_unit_id,
                'date' => $request->date,
                'slot_type' => $slotType,
                'time_slot' => $timeSlot,
            ],
            [
                'status' => 'blocked_offline',
                'notes' => $validated['notes'] ?? null,
            ]
        );

        return response()->json(['success' => true, 'slot' => $slot]);
    }

    public function unblockSlot(Request $request)
    {
        $validated = $request->validate([
            'hall_unit_id' => 'required|exists:hall_units,id',
            'date' => 'required|date',
            'slot_type' => 'nullable|in:all_day,noon,evening',
            'time_slot' => 'nullable|integer|min:0|max:23',
        ]);

        $slotType = $validated['slot_type'] ?? ($validated['time_slot'] !== null ? null : 'all_day');
        if ($slotType === 'all_day') {
            $slotType = null;
        }
        $timeSlot = $validated['time_slot'] ?? null;

        AvailabilitySlot::where([
            'resource_type' => 'App\Models\HallUnit',
            'resource_id' => $validated['hall_unit_id'],
            'date' => $validated['date'],
            'slot_type' => $slotType,
            'time_slot' => $timeSlot,
            'status' => 'blocked_offline',
        ])->delete();

        return response()->json(['success' => true]);
    }
}
