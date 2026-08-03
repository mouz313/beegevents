<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\AvailabilitySlot;
use App\Models\ExtraService;
use App\Models\HallUnit;
use App\Models\MenuSet;
use App\Models\ServiceListing;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);

        return view('customer.cart', compact('cart'));
    }

    public function addItem(Request $request)
    {
        $request->validate([
            'type' => 'required|in:hall_unit,service_listing',
            'id' => 'required|integer',
            'date' => 'required|date|after_or_equal:today',
            'time_slot' => 'nullable|in:noon,evening',
            'menu_set_id' => 'nullable|exists:menu_sets,id',
            'guests' => 'nullable|integer|min:1',
            'catering_mode' => 'nullable|in:internal,external,none',
            'extras' => 'nullable|array',
            'extras.*.id' => 'required|integer',
        ]);

        $cart = session()->get('cart', []);
        $cart = $this->resolveItemIntoCart($cart, $request->type, (int) $request->id, $request->date, $request);
        session()->put('cart', $cart);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'cart_count' => count($cart), 'cart' => $cart]);
        }

        return redirect()->back()->with('success', 'Item added to cart!');
    }

    public function addBundle(Request $request)
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'time_slot' => 'nullable|in:noon,evening',
            'items' => 'required|array|min:1',
            'items.*.type' => 'required|in:hall_unit,service_listing',
            'items.*.id' => 'required|integer',
        ]);

        $cart = session()->get('cart', []);

        foreach ($request->items as $item) {
            $cart = $this->resolveItemIntoCart($cart, $item['type'], (int) $item['id'], $request->date, $request);
        }

        session()->put('cart', $cart);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'cart_count' => count($cart), 'cart' => $cart]);
        }

        return redirect()->route('customer.cart')->with('success', 'Bundle added to cart!');
    }

    protected function resolveItemIntoCart(array $cart, string $type, int $id, string $date, Request $request): array
    {
        if ($type === 'hall_unit') {
            $item = HallUnit::with('hall.vendorProfile')->findOrFail($id);
            if (! $item->hall->vendorProfile || ! $item->hall->vendorProfile->visibleOnSite()) {
                abort(404);
            }
            $timeSlot = $request->input('time_slot') ?? 'noon';

            $guests = $request->input('guests');
            if ($guests !== null && $guests !== '') {
                $guests = (int) $guests;
                if ($guests < $item->min_capacity || $guests > $item->max_capacity) {
                    abort(422, $item->unit_name.' holds '.$item->min_capacity.'-'.$item->max_capacity.' guests. Please adjust the guest count.');
                }
            } else {
                $guests = null;
            }

            $cateringMode = $request->input('catering_mode');
            if ($cateringMode && ! $this->cateringModeAllowed($item, $cateringMode)) {
                abort(422, 'The selected catering option is not available for '.$item->unit_name.'.');
            }

            $conflict = AvailabilitySlot::where('resource_type', 'App\Models\HallUnit')
                ->where('resource_id', $item->id)
                ->where('date', $date)
                ->where(function ($q) use ($timeSlot) {
                    $q->where('slot_type', $timeSlot)->orWhereNull('slot_type');
                })
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
                ->exists();

            if ($conflict) {
                abort(409, $item->unit_name.' is not available for '.ucfirst($timeSlot).' on '.$date.'.');
            }

            $key = 'hall_unit_'.$item->id.'_'.$date.'_'.$timeSlot;

            // Validate menu set ownership + price
            $menuSetId = $request->input('menu_set_id');
            $menuSetPrice = 0;
            $menuSetName = null;
            if ($menuSetId) {
                $menuSet = MenuSet::with('items')->find($menuSetId);
                if ($menuSet && $menuSet->vendor_profile_id === $item->hall->vendor_profile_id) {
                    $menuSetPrice = $menuSet->getTotalPriceAttribute();
                    $menuSetName = $menuSet->name;
                } else {
                    $menuSetId = null;
                }
            }

            // Selected extra services (authoritative price from server, ownership-validated)
            $selectedExtraIds = collect($request->input('extras', []))->pluck('id')->filter()->unique();
            $unitExtras = ExtraService::where('serviceable_type', 'App\Models\HallUnit')
                ->where('serviceable_id', $item->id)
                ->whereIn('id', $selectedExtraIds)
                ->get();

            $extras = $unitExtras->map(function ($e) {
                return [
                    'id' => (int) $e->id,
                    'name' => $e->name,
                    'price' => (float) $e->price,
                    'price_unit' => $e->price_unit ?? 'flat',
                ];
            })->values()->all();

            $extrasTotal = array_sum(array_column($extras, 'price'));

            $cart[$key] = [
                'type' => 'hall_unit',
                'id' => $item->id,
                'date' => $date,
                'time_slot' => $timeSlot,
                'menu_set_id' => $menuSetId,
                'menu_set_name' => $menuSetName,
                'menu_set_price' => (float) $menuSetPrice,
                'extras' => $extras,
                'extras_total' => $extrasTotal,
                'guests' => $guests,
                'catering_mode' => $cateringMode,
                'name' => $item->hall->name.' - '.$item->unit_name,
                'price' => (float) $item->base_price,
                'capacity' => $item->min_capacity.'-'.$item->max_capacity,
                'vendor_id' => $item->hall->vendor_profile_id,
            ];
        } else {
            $item = ServiceListing::with('serviceCategory', 'vendorProfile')->findOrFail($id);
            if (! $item->vendorProfile || ! $item->vendorProfile->visibleOnSite()) {
                abort(404);
            }
            $key = 'listing_'.$item->id.'_'.$date;
            $cart[$key] = [
                'type' => 'service_listing',
                'id' => $item->id,
                'date' => $date,
                'name' => $item->title,
                'price' => $item->price,
                'price_unit' => $item->price_unit,
                'category' => $item->serviceCategory->name ?? '',
                'vendor_id' => $item->vendor_profile_id,
            ];
        }

        return $cart;
    }

    protected function cateringModeAllowed(HallUnit $unit, string $mode): bool
    {
        $unitMode = $unit->catering_mode;
        if (in_array($unitMode, ['internal', 'external', 'none'])) {
            return $mode === $unitMode;
        }

        return $unitMode === 'both';
    }

    public function removeItem(Request $request, $key)
    {
        $cart = session()->get('cart', []);
        unset($cart[$key]);
        session()->put('cart', $cart);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'cart_count' => count($cart)]);
        }

        return redirect()->route('customer.cart')->with('success', 'Item removed!');
    }

    public function clear()
    {
        session()->forget('cart');
        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('customer.cart')->with('success', 'Cart cleared!');
    }
}
