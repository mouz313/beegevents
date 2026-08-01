<?php

namespace Database\Seeders;

use App\Models\HallUnit;
use App\Models\Package;
use App\Models\PackageItem;
use App\Models\ServiceListing;
use Illuminate\Database\Seeder;

class PackageItemsSeeder extends Seeder
{
    public function run(): void
    {
        $map = [
            'Silver Wedding Package' => [
                ['hall_unit', 'Main Hall', 1],
                ['service_listing', 'Floral Arrangement Premium', null],
                ['service_listing', 'Standard Menu per Person', null],
            ],
            'Gold Wedding Package' => [
                ['hall_unit', 'Lawn', 1],
                ['service_listing', 'Stage Decoration Classic', null],
                ['service_listing', 'Premium Menu per Person', null],
                ['service_listing', 'Lighting Setup Deluxe', null],
            ],
            'Engagement Special' => [
                ['hall_unit', 'Lawn', 2],
                ['service_listing', 'Lighting Setup Deluxe', null],
                ['service_listing', 'Dessert Station Setup', null],
            ],
            'Corporate Event Package' => [
                ['hall_unit', 'Small Hall', 3],
                ['service_listing', 'Standard Menu per Person', null],
            ],
            'Birthday Bash' => [
                ['hall_unit', 'Lawn', 2],
                ['service_listing', 'Floral Arrangement Premium', null],
                ['service_listing', 'Dessert Station Setup', null],
            ],
            'Platinum Wedding' => [
                ['hall_unit', 'Main Hall', 3],
                ['service_listing', 'Premium Menu per Person', null],
                ['service_listing', 'Wedding Stage Theme Setup', null],
            ],
        ];

        $attached = 0;
        $skipped = [];

        foreach ($map as $packageTitle => $items) {
            $package = Package::where('title', $packageTitle)->first();
            if (! $package) {
                $skipped[] = "Package '$packageTitle' not found";
                continue;
            }

            foreach ($items as [$type, $name, $vendorId]) {
                if ($type === 'hall_unit') {
                    $itemable = HallUnit::where('unit_name', $name)
                        ->whereHas('hall', fn ($q) => $q->where('vendor_profile_id', $vendorId))
                        ->first();
                    $itemableType = 'App\Models\HallUnit';
                } else {
                    $itemable = ServiceListing::where('title', $name)->first();
                    $itemableType = 'App\Models\ServiceListing';
                }

                if (! $itemable) {
                    $skipped[] = "'$name' not found for '$packageTitle'";
                    continue;
                }

                $created = PackageItem::firstOrCreate([
                    'package_id' => $package->id,
                    'itemable_type' => $itemableType,
                    'itemable_id' => $itemable->id,
                ]);

                if ($created->wasRecentlyCreated) {
                    $attached++;
                }
            }
        }

        $this->command->info("Attached $attached package items.");
        if ($skipped) {
            $this->command->warn('Skipped: '.implode(' | ', $skipped));
        }
    }
}
