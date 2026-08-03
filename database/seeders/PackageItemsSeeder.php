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
            'Starter Plan' => [
                ['service_listing', 'Floral Arrangement Premium'],
                ['service_listing', 'Standard Menu per Person'],
            ],
            'Growth Plan' => [
                ['hall_unit', 'Main Hall'],
                ['service_listing', 'Stage Decoration Classic'],
                ['service_listing', 'Premium Menu per Person'],
                ['service_listing', 'Lighting Setup Deluxe'],
            ],
            'Premium Plan' => [
                ['hall_unit', 'Main Hall'],
                ['hall_unit', 'Lawn'],
                ['service_listing', 'Stage Decoration Classic'],
                ['service_listing', 'Premium Menu per Person'],
                ['service_listing', 'Lighting Setup Deluxe'],
                ['service_listing', 'Wedding Stage Theme Setup'],
            ],
            'Pro Annual' => [
                ['hall_unit', 'Main Hall'],
                ['hall_unit', 'Lawn'],
                ['service_listing', 'Stage Decoration Classic'],
                ['service_listing', 'Premium Menu per Person'],
                ['service_listing', 'Lighting Setup Deluxe'],
                ['service_listing', 'Wedding Stage Theme Setup'],
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

            foreach ($items as [$type, $name]) {
                if ($type === 'hall_unit') {
                    $itemable = HallUnit::where('unit_name', $name)->first();
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
