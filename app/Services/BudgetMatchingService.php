<?php

namespace App\Services;

use App\Models\HallUnit;
use App\Models\Package;
use App\Models\ServiceListing;
use Illuminate\Support\Collection;

class BudgetMatchingService
{
    public function match(float $budget, int $guestCount, ?string $eventType = null): array
    {
        $results = [];

        $hallUnits = HallUnit::with('hall.vendorProfile', 'extraServices')
            ->where('min_capacity', '<=', $guestCount)
            ->where('max_capacity', '>=', $guestCount)
            ->get()
            ->filter(fn ($unit) => $unit->base_price <= $budget)
            ->map(function ($unit) use ($budget) {
                $remaining = $budget - $unit->base_price;
                $suggestedExtras = $unit->extraServices->filter(fn ($extra) => $extra->price <= $remaining);
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
            ->filter(fn ($pkg) => $pkg->total_price <= $budget)
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

        usort($results, fn ($a, $b) => $a['total_estimated'] <=> $b['total_estimated']);

        return $results;
    }

    public function buildAutoPackage(float $budget, int $guestCount, ?string $eventType = null, ?string $city = null): array
    {
        $hall = $this->pickHallUnit($budget, $guestCount, $eventType, $city);
        $tag = 'within_budget';

        if ($hall) {
            $remaining = $budget - $hall->base_price;
            if ($remaining < 0) {
                $tag = $hall->base_price <= $budget * 1.2 ? 'slightly_above' : 'over_budget';
            }
        } else {
            $remaining = $budget;
            $tag = 'services_only';
        }

        $services = $this->greedyFill($remaining, $city);
        $total = round(($hall ? $hall->base_price : 0) + $services->sum('price'), 2);

        return [
            'status' => ($hall || $services->isNotEmpty()) ? 'matched' : 'none',
            'hall_unit' => $hall,
            'services' => $services->values(),
            'total' => $total,
            'budget' => $budget,
            'city' => $city,
            'tag' => $tag,
            'within_budget' => $total <= $budget,
        ];
    }

    protected function pickHallUnit(float $budget, int $guestCount, ?string $eventType = null, ?string $city = null): ?HallUnit
    {
        $units = HallUnit::with('hall.vendorProfile', 'extraServices')
            ->whereHas('hall.vendorProfile', function ($q) use ($city) {
                $q->where('status', 'verified');
                if ($city) {
                    $q->where('city', $city);
                }
            })
            ->where('min_capacity', '<=', $guestCount)
            ->where('max_capacity', '>=', $guestCount)
            ->get();

        $within = $units
            ->filter(fn ($unit) => $unit->base_price <= $budget)
            ->sortBy(fn ($unit) => abs($unit->base_price - $budget))
            ->first();
        if ($within) {
            return $within;
        }

        $slightlyAbove = $units
            ->filter(fn ($unit) => $unit->base_price <= $budget * 1.2)
            ->sortBy(fn ($unit) => $unit->base_price - $budget)
            ->first();
        if ($slightlyAbove) {
            return $slightlyAbove;
        }

        return $units
            ->sortBy(fn ($unit) => $unit->base_price - $budget)
            ->first();
    }

    protected function greedyFill(float $budget, ?string $city = null): Collection
    {
        if ($budget <= 0) {
            return collect();
        }

        $listings = ServiceListing::with('serviceCategory', 'vendorProfile')
            ->whereHas('vendorProfile', function ($q) use ($city) {
                $q->where('status', 'verified');
                if ($city) {
                    $q->where('city', $city);
                }
            })
            ->get()
            ->filter(fn ($l) => $l->price <= $budget);

        $chosen = collect();
        $remaining = $budget;
        $usedCategories = collect();
        $byCategory = $listings->groupBy('service_category_id');
        $categoryIds = $byCategory->keys();

        $progress = true;
        while ($progress && $remaining > 0) {
            $progress = false;
            foreach ($categoryIds as $catId) {
                if ($usedCategories->contains($catId)) {
                    continue;
                }

                $pick = $byCategory[$catId]
                    ->filter(fn ($l) => $l->price <= $remaining)
                    ->sortBy('price')
                    ->first();

                if (! $pick) {
                    continue;
                }

                $chosen->push($pick);
                $usedCategories->push($catId);
                $remaining -= $pick->price;
                $progress = true;

                if ($remaining <= 0) {
                    break;
                }
            }
        }

        return $chosen;
    }
}
