<?php

namespace App\Services;

use App\Models\HallUnit;
use App\Models\Package;

class BudgetMatchingService
{
    public function match(float $budget, int $guestCount, ?string $eventType = null): array
    {
        $results = [];

        $hallUnits = HallUnit::with('hall.vendorProfile', 'extraServices')
            ->where('min_capacity', '<=', $guestCount)
            ->where('max_capacity', '>=', $guestCount)
            ->get()
            ->filter(fn($unit) => $unit->base_price <= $budget)
            ->map(function ($unit) use ($budget) {
                $remaining = $budget - $unit->base_price;
                $suggestedExtras = $unit->extraServices->filter(fn($extra) => $extra->price <= $remaining);
                $totalWithExtras = $unit->base_price + $suggestedExtras->sum('price');
                return [
                    'type' => 'unit',
                    'unit' => $unit,
                    'vendor' => $unit->hall->vendorProfile ?? null,
                    'base_price' => $unit->base_price,
                    'suggested_extras' => $suggestedExtras,
                    'total_estimated' => $totalWithExtras,
                    'within_budget' => $totalWithExtras <= $budget,
                ];
            })
            ->sortBy('total_estimated')
            ->values()
            ->toArray();

        $results = array_merge($results, $hallUnits);

        $packageQuery = Package::with('packageItems');
        if ($eventType) {
            $packageQuery->where('event_type', $eventType);
        }

        $packages = $packageQuery->get()
            ->filter(fn($pkg) => $pkg->total_price <= $budget)
            ->map(function ($pkg) {
                $itemsBreakdown = $pkg->packageItems->map(function ($item) {
                    $modelClass = $item->itemable_type;
                    $instance = $modelClass::find($item->itemable_id);
                    $name = $instance ? ($instance->unit_name ?? $instance->title ?? class_basename($item->itemable_type).' #'.$item->itemable_id) : 'Item #'.$item->itemable_id;
                    $price = $instance->base_price ?? $instance->price ?? 0;
                    $vendorName = null;
                    if ($instance && method_exists($instance, 'hall') && $instance->hall->vendorProfile ?? null) {
                        $vendorName = $instance->hall->vendorProfile->business_name;
                    } elseif ($instance && method_exists($instance, 'vendorProfile')) {
                        $vendorName = $instance->vendorProfile->business_name;
                    }
                    return [
                        'name' => $name,
                        'vendor' => $vendorName,
                        'price' => $price,
                    ];
                });

                return [
                    'type' => 'package',
                    'package' => $pkg,
                    'items' => $itemsBreakdown,
                    'total_estimated' => $pkg->total_price,
                    'within_budget' => $pkg->total_price <= $budget,
                ];
            })
            ->sortBy('total_estimated')
            ->values()
            ->toArray();

        $results = array_merge($results, $packages);

        usort($results, fn($a, $b) => $a['total_estimated'] <=> $b['total_estimated']);

        return $results;
    }
}
