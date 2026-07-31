<?php

namespace App\Http\Controllers;

use App\Models\HallUnit;
use App\Models\Package;
use App\Models\ServiceCategory;
use App\Services\BudgetMatchingService;
use Illuminate\Http\Request;

class BookingOptionsController extends Controller
{
    public function options(Request $request)
    {
        $hallId = $request->integer('hall_id') ?: null;

        $packages = Package::with(['vendorProfile', 'packageItems'])
            ->where(function ($q) {
                $q->whereNull('vendor_profile_id')
                    ->orWhereHas('vendorProfile', fn ($v) => $v->where('status', 'verified'));
            })
            ->when($hallId, function ($q) use ($hallId) {
                $unitIds = HallUnit::where('hall_id', $hallId)->pluck('id');
                $q->whereHas('packageItems', fn ($p) => $p
                    ->where('itemable_type', HallUnit::class)
                    ->whereIn('itemable_id', $unitIds));
            })
            ->get()
            ->map(function ($pkg) {
                return [
                    'id' => $pkg->id,
                    'title' => $pkg->title,
                    'description' => $pkg->description,
                    'total_price' => (float) $pkg->total_price,
                    'event_type' => $pkg->event_type,
                    'vendor' => $pkg->vendorProfile ? [
                        'id' => $pkg->vendorProfile->id,
                        'business_name' => $pkg->vendorProfile->business_name,
                    ] : null,
                    'items' => $pkg->packageItems->map(function ($item) {
                        $instance = $item->itemable;
                        if (! $instance) {
                            return null;
                        }

                        return [
                            'type' => $item->itemable_type === HallUnit::class ? 'hall_unit' : 'service_listing',
                            'name' => $instance->unit_name ?? $instance->title ?? '',
                            'price' => (float) ($instance->base_price ?? $instance->price ?? 0),
                        ];
                    })->filter()->values(),
                ];
            })
            ->values();

        $categories = ServiceCategory::with(['serviceListings' => function ($q) {
            $q->whereHas('vendorProfile', fn ($v) => $v->where('status', 'verified'));
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
                    ];
                })->values(),
            ];
        })->values();

        return response()->json([
            'packages' => $packages,
            'categories' => $categories,
        ]);
    }

    public function budget(Request $request, BudgetMatchingService $matcher)
    {
        $validated = $request->validate([
            'budget' => 'required|numeric|min:1',
            'guest_count' => 'required|integer|min:1',
            'event_type' => 'nullable|string',
        ]);

        $bundle = $matcher->buildAutoPackage(
            (float) $validated['budget'],
            (int) $validated['guest_count'],
            $validated['event_type'] ?: null
        );

        return response()->json(['bundle' => $bundle]);
    }
}
