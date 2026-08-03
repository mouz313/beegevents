<?php

namespace App\Http\Controllers;

use App\Models\ServiceCategory;
use App\Models\VendorProfile;
use App\Services\BudgetMatchingService;
use Illuminate\Http\Request;

class BookingOptionsController extends Controller
{
    public function options(Request $request)
    {
        $categories = ServiceCategory::with(['serviceListings' => function ($q) {
            $q->whereHas('vendorProfile', fn ($v) => $v->visible());
        }])->get()->map(function ($cat) {
            return [
                'id' => $cat->id,
                'name' => $cat->name,
                'slug' => $cat->slug,
                'listings' => $cat->serviceListings->map(function ($listing) {
                    return [
                        'id' => $listing->id,
                        'title' => $listing->title,
                        'description' => $listing->description,
                        'price' => (float) $listing->price,
                        'price_unit' => $listing->price_unit,
                        'vendor' => $listing->vendorProfile->business_name ?? '',
                        'city' => $listing->vendorProfile->city ?? '',
                    ];
                })->values(),
            ];
        })->values();

        $cities = VendorProfile::whereNotNull('city')
            ->where('city', '!=', '')
            ->visible()
            ->distinct()
            ->orderBy('city')
            ->pluck('city')
            ->values();

        return response()->json([
            'categories' => $categories,
            'cities' => $cities,
        ]);
    }

    public function hallOptions(Request $request)
    {
        $request->validate(['hall_id' => 'required|exists:halls,id']);

        $hall = \App\Models\Hall::with('vendorProfile', 'hallUnits.extraServices')->findOrFail($request->integer('hall_id'));

        if (! $hall->vendorProfile || ! $hall->vendorProfile->visibleOnSite()) {
            abort(404);
        }

        $menuSets = $hall->vendorProfile
            ? $hall->vendorProfile->menuSets()->with('items')->where('is_active', true)->get()
            : collect();

        $amenityLabels = [
            'parking' => 'Parking', 'wheelchair' => 'Wheelchair Access', 'ac' => 'AC / Heating',
            'sound_system' => 'Sound System', 'generator' => 'Generator Backup', 'bridal_room' => 'Bridal Room',
            'stage' => 'Stage', 'washrooms' => 'Washrooms', 'waiting_area' => 'Waiting Area', 'dining_tables' => 'Dining Tables',
        ];

        return response()->json([
            'hall' => [
                'id' => $hall->id,
                'name' => $hall->name,
                'address' => $hall->address,
                'description' => $hall->description,
            ],
            'units' => $hall->hallUnits->map(function ($unit) use ($menuSets, $amenityLabels) {
                return [
                    'id' => $unit->id,
                    'name' => $unit->unit_name,
                    'capacity' => $unit->min_capacity.'-'.$unit->max_capacity,
                    'min_capacity' => (int) $unit->min_capacity,
                    'max_capacity' => (int) $unit->max_capacity,
                    'base_price' => (float) $unit->base_price,
                    'decor_type' => $unit->decor_type,
                    'catering_mode' => $unit->catering_mode,
                    'food_service_style' => $unit->food_service_style,
                    'staff_male' => (int) $unit->staff_male,
                    'staff_female' => (int) $unit->staff_female,
                    'amenities' => collect($unit->amenities ?? [])->map(fn ($a) => $amenityLabels[$a] ?? ucfirst(str_replace('_', ' ', $a)))->values(),
                    'menu_sets' => $menuSets->map(fn ($set) => [
                        'id' => $set->id,
                        'name' => $set->name,
                        'price' => $set->getTotalPriceAttribute(),
                    ])->values(),
                    'extras' => $unit->extraServices->map(fn ($e) => [
                        'id' => $e->id,
                        'name' => $e->name,
                        'price' => (float) $e->price,
                        'price_unit' => $e->price_unit ?? 'flat',
                    ])->values(),
                ];
            })->values(),
        ]);
    }

    public function budget(Request $request, BudgetMatchingService $matcher)
    {
        $validated = $request->validate([
            'budget' => 'required|numeric|min:1',
            'guest_count' => 'required|integer|min:1',
            'event_type' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'date' => 'nullable|date|after_or_equal:today',
            'time_slot' => 'nullable|in:noon,evening',
        ]);

        $bundle = $matcher->buildAutoPackage(
            (float) $validated['budget'],
            (int) $validated['guest_count'],
            ($validated['event_type'] ?? null) ?: null,
            ($validated['city'] ?? null) ?: null,
            ($validated['date'] ?? null) ?: null,
            ($validated['time_slot'] ?? null) ?: null
        );

        return response()->json(['bundle' => $bundle]);
    }
}
