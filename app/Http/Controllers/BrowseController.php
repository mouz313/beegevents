<?php

namespace App\Http\Controllers;

use App\Models\AvailabilitySlot;
use App\Models\BookingItem;
use App\Models\Hall;
use App\Models\Package;
use App\Models\Review;
use App\Models\ServiceCategory;
use App\Models\ServiceListing;
use Illuminate\Http\Request;

class BrowseController extends Controller
{
    public function index(Request $request)
    {
        $categories = ServiceCategory::all();
        $halls = Hall::with('vendorProfile', 'hallUnits', 'hallImages', 'floors')->get();
        $listings = ServiceListing::with('vendorProfile', 'serviceCategory')->get();

        return view('browse.index', compact('categories', 'halls', 'listings'));
    }

    public function category(Request $request, $slug)
    {
        $category = ServiceCategory::where('slug', $slug)->firstOrFail();

        if ($slug === 'hall') {
            $halls = Hall::with('vendorProfile', 'hallUnits')->get();

            return view('browse.halls', compact('category', 'halls'));
        }

        $listings = ServiceListing::with('vendorProfile')->where('service_category_id', $category->id)->get();

        return view('browse.listings', compact('category', 'listings'));
    }

    public function hallDetail(Request $request, Hall $hall)
    {
        $selectedDate = $request->get('date', date('Y-m-d', strtotime('+1 day')));

        $hall->load('vendorProfile', 'floors', 'hallUnits.floor', 'hallImages');
        $dateList = $this->buildHallDateList($hall);

        // Per-unit booked dates for client-side checking
        $unitIds = $hall->hallUnits->pluck('id');
        $allBookedItems = BookingItem::where('itemable_type', 'App\Models\HallUnit')
            ->whereIn('itemable_id', $unitIds)
            ->whereHas('booking', fn ($q) => $q->whereNotIn('status', ['cancelled']))
            ->with('booking')
            ->get();

        $unitBookedDates = [];
        foreach ($unitIds as $uid) {
            $unitBookedDates[$uid] = [];
        }
        foreach ($allBookedItems as $bi) {
            $unitBookedDates[$bi->itemable_id][] = $bi->booking->event_date->format('Y-m-d');
        }

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
            $similarHalls = Hall::with('vendorProfile', 'hallUnits')
                ->where('id', '!=', $hall->id)
                ->whereHas('vendorProfile', fn ($q) => $q->where('city', $city))
                ->take(6)
                ->get();
        }

        return view('browse.hall-detail', compact(
            'hall', 'dateList', 'unitBookedDates', 'selectedDate',
            'reviews', 'avgRating', 'totalReviews', 'similarHalls'
        ));
    }

    public function listingDetail(Request $request, ServiceListing $listing)
    {
        $selectedDate = $request->get('date', date('Y-m-d', strtotime('+1 day')));

        $listing->load('vendorProfile', 'serviceCategory');
        $dateList = $this->buildListingDateList($listing);

        return view('browse.listing-detail', compact('listing', 'dateList', 'selectedDate'));
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

    public function packages()
    {
        $packages = Package::with('packageItems')->latest()->get();

        return view('browse.packages', compact('packages'));
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

        // ---- Halls ----
        $hallsQuery = Hall::with('vendorProfile', 'hallUnits');

        if ($query) {
            $hallsQuery->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('address', 'like', "%{$query}%");
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

        // Date availability filter
        $unavailableHallUnitIds = collect();
        if ($date) {
            $unavailableHallUnitIds = BookingItem::where('itemable_type', 'App\Models\HallUnit')
                ->whereHas('booking', function ($q) use ($date) {
                    $q->where('event_date', $date)
                        ->whereNotIn('status', ['cancelled']);
                })
                ->pluck('itemable_id');

            $hallsQuery->where(function ($q) use ($unavailableHallUnitIds) {
                $q->whereDoesntHave('hallUnits')
                    ->orWhereHas('hallUnits', function ($q) use ($unavailableHallUnitIds) {
                        $q->whereNotIn('id', $unavailableHallUnitIds);
                    });
            });
        }

        $halls = $hallsQuery->get();

        // Annotate halls with available unit count for the date
        $halls->each(function ($hall) use ($unavailableHallUnitIds, $date) {
            $hall->available_units = $date
                ? $hall->hallUnits->whereNotIn('id', $unavailableHallUnitIds)->count()
                : $hall->hallUnits->count();
            $hall->total_units = $hall->hallUnits->count();
        });

        // ---- Listings ----
        $listingsQuery = ServiceListing::with('vendorProfile', 'serviceCategory');

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

        $listings = $listingsQuery->get();

        if ($request->ajax()) {
            return response()->json(compact('query', 'date', 'eventType', 'halls', 'listings'));
        }

        return view('browse.search', compact('query', 'date', 'eventType', 'city', 'maxBudget', 'minPrice', 'guests', 'halls', 'listings'));
    }
}
