<?php

namespace App\Http\Controllers;

use App\Models\AvailabilitySlot;
use App\Models\BookingItem;
use App\Models\Hall;
use App\Models\Review;
use App\Models\ServiceCategory;
use App\Models\ServiceListing;
use Illuminate\Http\Request;

class BrowseController extends Controller
{
    public function index(Request $request)
    {
        $categories = ServiceCategory::all();
        $halls = $this->featuredFirst(Hall::with('vendorProfile', 'hallUnits', 'hallImages', 'floors')
            ->whereHas('vendorProfile', fn ($q) => $q->visible())
            ->get());
        $listings = $this->featuredFirst(ServiceListing::with('vendorProfile', 'serviceCategory')
            ->whereHas('vendorProfile', fn ($q) => $q->visible())
            ->get());

        return view('browse.index', compact('categories', 'halls', 'listings'));
    }

    public function category(Request $request, $slug)
    {
        $category = ServiceCategory::where('slug', $slug)->firstOrFail();

        if ($slug === 'hall') {
            $halls = $this->featuredFirst(Hall::with('vendorProfile', 'hallUnits')
                ->whereHas('vendorProfile', fn ($q) => $q->visible())
                ->get());

            return view('browse.halls', compact('category', 'halls'));
        }

        $listings = $this->featuredFirst(ServiceListing::with('vendorProfile')
            ->where('service_category_id', $category->id)
            ->whereHas('vendorProfile', fn ($q) => $q->visible())
            ->get());

        return view('browse.listings', compact('category', 'listings'));
    }

    public function hallDetail(Request $request, Hall $hall)
    {
        $selectedDate = $request->get('date', date('Y-m-d', strtotime('+1 day')));
        $selectedTimeSlot = in_array($request->get('time_slot'), ['noon', 'evening']) ? $request->get('time_slot') : null;

        $hall->load('vendorProfile', 'floors', 'hallUnits.floor', 'hallImages');
        if (! $hall->vendorProfile || ! $hall->vendorProfile->visibleOnSite()) {
            abort(404);
        }
        $dateList = $this->buildHallDateList($hall);

        // Per-unit booked slots (date + time_slot) for client-side checking
        $unitIds = $hall->hallUnits->pluck('id');
        $allBookedItems = BookingItem::where('itemable_type', 'App\Models\HallUnit')
            ->whereIn('itemable_id', $unitIds)
            ->whereHas('booking', fn ($q) => $q->whereNotIn('status', ['cancelled']))
            ->with('booking')
            ->get();

        $unitBookedSlots = [];
        foreach ($unitIds as $uid) {
            $unitBookedSlots[$uid] = [];
        }
        foreach ($allBookedItems as $bi) {
            $date = $bi->booking->event_date->format('Y-m-d');
            $slot = $bi->time_slot ?? 'noon';
            $unitBookedSlots[$bi->itemable_id][$date] = array_values(array_unique(array_merge(
                $unitBookedSlots[$bi->itemable_id][$date] ?? [],
                [$slot]
            )));
        }

        // Menu sets (active) offered by this hall's vendor
        $menuSets = $hall->vendorProfile
            ? $hall->vendorProfile->menuSets()->with(['items.menuCategory'])->where('is_active', true)->orderBy('sort_order')->get()
            : collect();

        // Reviews
        $vendorProfileId = $hall->vendor_profile_id;
        $reviews = Review::with('customer')
            ->where('vendor_profile_id', $vendorProfileId)
            ->latest()
            ->take(10)
            ->get();
        $avgRating = Review::where('vendor_profile_id', $vendorProfileId)->avg('rating') ?? 0;
        $totalReviews = Review::where('vendor_profile_id', $vendorProfileId)->count();

        // Similar halls (same city, exclude current)
        $city = $hall->vendorProfile->city ?? '';
        $similarHalls = collect();
        if ($city) {
            $similarHalls = $this->featuredFirst(Hall::with('vendorProfile', 'hallUnits')
                ->where('id', '!=', $hall->id)
                ->whereHas('vendorProfile', fn ($q) => $q->where('city', $city)->visible())
                ->take(6)
                ->get());
        }

        return view('browse.hall-detail', compact(
            'hall', 'dateList', 'unitBookedSlots', 'menuSets', 'selectedDate', 'selectedTimeSlot',
            'reviews', 'avgRating', 'totalReviews', 'similarHalls'
        ));
    }

    public function listingDetail(Request $request, ServiceListing $listing)
    {
        $selectedDate = $request->get('date', date('Y-m-d', strtotime('+1 day')));

        $listing->load('vendorProfile', 'serviceCategory');
        if (! $listing->vendorProfile || ! $listing->vendorProfile->visibleOnSite()) {
            abort(404);
        }
        $dateList = $this->buildListingDateList($listing);

        return view('browse.listing-detail', compact('listing', 'dateList', 'selectedDate'));
    }

    /**
     * Reorder a collection so actively boosted vendors (premium > featured) come first.
     */
    private function featuredFirst($collection)
    {
        return $collection->sortByDesc(function ($item) {
            $profile = $item->vendorProfile;
            $tier = $profile && $profile->isFeatured() ? $profile->feature_tier : null;

            if ($tier === 'premium') {
                return 2;
            }
            if ($tier === 'featured') {
                return 1;
            }

            return 0;
        })->values();
    }

    private function slotIsUnavailable($slot): bool
    {
        if (in_array($slot->status, ['booked', 'blocked_offline'])) {
            return true;
        }

        if ($slot->status === 'held') {
            return $slot->held_until === null || $slot->held_until->gt(now());
        }

        return false;
    }

    private function buildHallDateList(Hall $hall)
    {
        $unitIds = $hall->hallUnits->pluck('id');
        $total = $unitIds->count();
        if ($total === 0) {
            return [];
        }

        $bookedPerDate = BookingItem::where('itemable_type', 'App\Models\HallUnit')
            ->whereIn('itemable_id', $unitIds)
            ->whereHas('booking', fn ($q) => $q->whereNotIn('status', ['cancelled']))
            ->with('booking')
            ->get()
            ->groupBy(fn ($i) => $i->booking->event_date->format('Y-m-d'))
            ->map->count();

        $slotData = AvailabilitySlot::where('resource_type', 'App\Models\HallUnit')
            ->whereIn('resource_id', $unitIds)
            ->where('date', '>=', now()->startOfDay())
            ->get()
            ->filter(fn ($s) => $this->slotIsUnavailable($s))
            ->groupBy('date')
            ->map->count();

        $list = [];
        $start = now()->copy()->addDay();
        $end = now()->copy()->addYear();

        $d = $start->copy();
        while ($d <= $end) {
            $dateStr = $d->format('Y-m-d');
            $b = $bookedPerDate->get($dateStr, 0);
            $s = $slotData->get($dateStr, 0);
            $cnt = max($b, $s);

            if ($cnt >= $total) {
                $info = ['status' => 'booked', 'class' => 'dt-booked', 'label' => 'All booked', 'total' => $total, 'booked' => $cnt];
            } elseif ($cnt > 0) {
                $avail = $total - $cnt;
                $info = ['status' => 'partial', 'class' => 'dt-partial', 'label' => "{$avail}/{$total} available", 'total' => $total, 'booked' => $cnt];
            } else {
                $info = ['status' => 'available', 'class' => 'dt-avail', 'label' => 'Available', 'total' => $total, 'booked' => 0];
            }

            $list[] = [
                'date' => $dateStr,
                'day' => (int) $d->format('j'),
                'day_name' => $d->format('D'),
                'month' => $d->format('M'),
                'status' => $info['status'],
                'class' => $info['class'],
                'label' => $info['label'],
                'total' => $total,
                'booked' => $info['booked'],
            ];

            $d->addDay();
        }

        return $list;
    }

    private function buildListingDateList(ServiceListing $listing)
    {
        $bookedDates = BookingItem::where('itemable_type', 'App\Models\ServiceListing')
            ->where('itemable_id', $listing->id)
            ->whereHas('booking', fn ($q) => $q->whereNotIn('status', ['cancelled']))
            ->with('booking')
            ->get()
            ->map(fn ($i) => $i->booking->event_date->format('Y-m-d'))
            ->unique()
            ->values();

        $slotData = AvailabilitySlot::where('resource_type', 'App\Models\ServiceListing')
            ->where('resource_id', $listing->id)
            ->where('date', '>=', now()->startOfDay())
            ->get()
            ->filter(fn ($s) => $this->slotIsUnavailable($s))
            ->map(fn ($s) => $s->date->format('Y-m-d'));

        $list = [];
        $start = now()->copy()->addDay();
        $end = now()->copy()->addYear();

        $d = $start->copy();
        while ($d <= $end) {
            $dateStr = $d->format('Y-m-d');

            if ($bookedDates->contains($dateStr) || $slotData->contains($dateStr)) {
                $info = ['status' => 'booked', 'class' => 'dt-booked', 'label' => 'Booked'];
            } else {
                $info = ['status' => 'available', 'class' => 'dt-avail', 'label' => 'Available'];
            }

            $list[] = [
                'date' => $dateStr,
                'day' => (int) $d->format('j'),
                'day_name' => $d->format('D'),
                'month' => $d->format('M'),
                'status' => $info['status'],
                'class' => $info['class'],
                'label' => $info['label'],
            ];

            $d->addDay();
        }

        return $list;
    }

    public function search(Request $request)
    {
        $query = $request->get('q');
        $date = $request->get('date');
        $eventType = $request->get('event_type');
        $city = $request->get('city');
        $maxBudget = $request->get('max_budget');
        $minPrice = $request->get('min_price');
        $guests = $request->get('guests');
        $venueType = $request->get('venue_type');
        $timeSlot = $request->get('time_slot');
        $amenities = $request->input('amenities', []);

        // ---- Halls ----
        $hallsQuery = Hall::with('vendorProfile', 'hallUnits')
            ->whereHas('vendorProfile', fn ($q) => $q->visible());

        if ($query) {
            $hallsQuery->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('address', 'like', "%{$query}%");
            });
        }

        if ($venueType) {
            $hallsQuery->where('venue_type', $venueType);
        }

        if (count($amenities)) {
            $hallsQuery->whereHas('hallUnits', function ($q) use ($amenities) {
                $q->where(function ($q) use ($amenities) {
                    foreach ($amenities as $am) {
                        $q->whereJsonContains('amenities', $am);
                    }
                });
            });
        }

        if ($city) {
            $hallsQuery->whereHas('vendorProfile', function ($q) use ($city) {
                $q->where('city', 'like', "%{$city}%");
            });
        }

        if ($maxBudget) {
            $budget = (int) $maxBudget;
            $hallsQuery->where(function ($q) use ($budget) {
                $q->whereHas('vendorProfile', function ($p) use ($budget) {
                    $p->where('starting_price', '<=', $budget);
                })->orWhereHas('hallUnits', function ($u) use ($budget) {
                    $u->where('base_price', '<=', $budget);
                });
            });
        }

        if ($minPrice) {
            $min = (int) $minPrice;
            $hallsQuery->where(function ($q) use ($min) {
                $q->whereHas('vendorProfile', function ($p) use ($min) {
                    $p->where('starting_price', '>=', $min);
                })->orWhereHas('hallUnits', function ($u) use ($min) {
                    $u->where('base_price', '>=', $min);
                });
            });
        }

        if ($guests) {
            $g = (int) $guests;
            $hallsQuery->where(function ($q) use ($g) {
                $q->whereHas('vendorProfile', function ($p) use ($g) {
                    $p->where('min_capacity', '<=', $g)
                        ->where('max_capacity', '>=', $g);
                })->orWhereHas('hallUnits', function ($u) use ($g) {
                    $u->where('max_capacity', '>=', $g)
                        ->where('min_capacity', '<=', $g);
                });
            });
        }

        // Date availability filter (slot-aware; does not over-restrict "any time" searches)
        $unitTakenMap = collect();
        if ($date) {
            // slot markers explicitly taken (slot_type noon/evening) or blocking the whole day (slot_type null)
            $slotTaken = AvailabilitySlot::where('resource_type', 'App\Models\HallUnit')
                ->where('date', $date)
                ->get()
                ->filter(fn ($s) => $this->slotIsUnavailable($s));

            // booking items taking a specific slot on this date
            $itemTaken = BookingItem::where('itemable_type', 'App\Models\HallUnit')
                ->whereHas('booking', function ($q) use ($date) {
                    $q->where('event_date', $date)->whereNotIn('status', ['cancelled']);
                })
                ->get();

            $taken = [];
            foreach ($slotTaken as $s) {
                $taken[(int) $s->resource_id] = $taken[(int) $s->resource_id] ?? ['noon' => false, 'evening' => false];
                if ($s->slot_type === null) {
                    $taken[(int) $s->resource_id]['noon'] = true;
                    $taken[(int) $s->resource_id]['evening'] = true;
                } elseif (in_array($s->slot_type, ['noon', 'evening'])) {
                    $taken[(int) $s->resource_id][$s->slot_type] = true;
                }
            }
            foreach ($itemTaken as $bi) {
                $slot = $bi->time_slot ?? 'noon';
                if (! in_array($slot, ['noon', 'evening'])) {
                    continue;
                }
                $taken[(int) $bi->itemable_id] = $taken[(int) $bi->itemable_id] ?? ['noon' => false, 'evening' => false];
                $taken[(int) $bi->itemable_id][$slot] = true;
            }

            $unitTakenMap = collect($taken);
        }

        // Which hall units to hide for the chosen search
        $hiddenHallUnitIds = collect();
        if ($date) {
            if ($timeSlot) {
                // Only the exact slot is a problem
                $hiddenHallUnitIds = $unitTakenMap->filter(function ($slots) use ($timeSlot) {
                    return ! empty($slots[$timeSlot]);
                })->keys();
            } else {
                // "Any time" — a unit is only fully ruled out when both noon AND evening are taken
                $hiddenHallUnitIds = $unitTakenMap->filter(function ($slots) {
                    return ! empty($slots['noon']) && ! empty($slots['evening']);
                })->keys();
            }

            $hiddenHallUnitIds = $hiddenHallUnitIds->map(fn ($id) => (int) $id);

            $hallsQuery->where(function ($q) use ($hiddenHallUnitIds) {
                $q->whereDoesntHave('hallUnits')
                    ->orWhereHas('hallUnits', function ($q) use ($hiddenHallUnitIds) {
                        $q->whereNotIn('id', $hiddenHallUnitIds);
                    });
            });
        }

        $halls = $this->featuredFirst($hallsQuery->get());

        // Annotate halls with available unit count for the date/slot
        $halls->each(function ($hall) use ($hiddenHallUnitIds, $date, $timeSlot) {
            $hidden = $date ? $hiddenHallUnitIds : collect();
            $hall->available_units = $date
                ? $hall->hallUnits->whereNotIn('id', $hidden)->count()
                : $hall->hallUnits->count();
            $hall->total_units = $hall->hallUnits->count();
            $hall->searched_time_slot = $timeSlot;
        });

        // ---- Listings ----
        $listingsQuery = ServiceListing::with('vendorProfile', 'serviceCategory')
            ->whereHas('vendorProfile', fn ($q) => $q->visible());

        if ($query) {
            $listingsQuery->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                    ->orWhereHas('vendorProfile', function ($q) use ($query) {
                        $q->where('business_name', 'like', "%{$query}%");
                    });
            });
        }

        if ($city) {
            $listingsQuery->whereHas('vendorProfile', function ($q) use ($city) {
                $q->where('city', 'like', "%{$city}%");
            });
        }

        if ($maxBudget) {
            $budget = (int) $maxBudget;
            $listingsQuery->where(function ($q) use ($budget) {
                $q->where('price', '<=', $budget)
                    ->orWhereHas('vendorProfile', function ($p) use ($budget) {
                        $p->where('starting_price', '<=', $budget);
                    });
            });
        }

        if ($minPrice) {
            $min = (int) $minPrice;
            $listingsQuery->where(function ($q) use ($min) {
                $q->where('price', '>=', $min)
                    ->orWhereHas('vendorProfile', function ($p) use ($min) {
                        $p->where('starting_price', '>=', $min);
                    });
            });
        }

        if ($guests) {
            $g = (int) $guests;
            $listingsQuery->whereHas('vendorProfile', function ($p) use ($g) {
                $p->where('min_capacity', '<=', $g)
                    ->where('max_capacity', '>=', $g);
            });
        }

        if ($eventType) {
            $categorySlugMap = [
                'wedding' => 'hall',
                'engagement' => 'decor',
                'corporate' => 'catering',
                'birthday' => 'photography',
            ];
            $slug = $categorySlugMap[$eventType] ?? null;
            if ($slug) {
                $catId = ServiceCategory::where('slug', $slug)->first()?->id;
                if ($catId) {
                    $listingsQuery->where('service_category_id', $catId);
                }
            }
        }

        // Date availability for listings
        $unavailableListingIds = collect();
        if ($date) {
            $unavailableListingIds = BookingItem::where('itemable_type', 'App\Models\ServiceListing')
                ->whereHas('booking', function ($q) use ($date) {
                    $q->where('event_date', $date)
                        ->whereNotIn('status', ['cancelled']);
                })
                ->pluck('itemable_id');

            $listingsQuery->whereNotIn('id', $unavailableListingIds);
        }

        $listings = $this->featuredFirst($listingsQuery->get());

        if ($request->ajax()) {
            return response()->json(compact('query', 'date', 'eventType', 'venueType', 'timeSlot', 'amenities', 'halls', 'listings'));
        }

        return view('browse.search', compact('query', 'date', 'eventType', 'venueType', 'timeSlot', 'amenities', 'city', 'maxBudget', 'minPrice', 'guests', 'halls', 'listings'));
    }
}
