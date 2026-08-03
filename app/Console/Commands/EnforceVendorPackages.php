<?php

namespace App\Console\Commands;

use App\Models\VendorPackagePurchase;
use App\Models\VendorProfile;
use App\Services\VendorPackagePurchaseService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:enforce-vendor-packages')]
#[Description('Expire vendor packages and block vendors whose trial and package have both ended')]
class EnforceVendorPackages extends Command
{
    public function handle(VendorPackagePurchaseService $service)
    {
        $expired = VendorPackagePurchase::where('status', 'active')
            ->where('ends_at', '<', now())
            ->get();

        foreach ($expired as $purchase) {
            $service->expire($purchase);
        }

        $blocked = 0;

        VendorProfile::query()
            ->whereIn('status', ['verified', 'blocked'])
            ->where(function ($q) {
                $q->whereNull('trial_ends_at')
                    ->orWhere('trial_ends_at', '<', now());
            })
            ->whereDoesntHave('packagePurchases', function ($q) {
                $q->where('status', 'active')->where('ends_at', '>', now());
            })
            ->each(function (VendorProfile $vendor) use (&$blocked) {
                if ($vendor->status !== 'blocked') {
                    $vendor->update([
                        'status' => 'blocked',
                        'feature_tier' => null,
                        'featured_until' => null,
                    ]);
                    $blocked++;
                }
            });

        $this->info("Expired {$expired->count()} package(s), blocked {$blocked} vendor(s).");
    }
}
