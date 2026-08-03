<?php

namespace App\Services;

use App\Models\Package;
use App\Models\VendorPackagePurchase;
use App\Models\VendorProfile;
use Stripe\StripeClient;

class VendorPackagePurchaseService
{
    protected ?StripeClient $stripe = null;

    public function __construct()
    {
        $secret = config('services.stripe.secret');
        if ($secret) {
            $this->stripe = new StripeClient($secret);
        }
    }

    public function createStripePaymentIntent(Package $package, VendorProfile $vendor): array
    {
        if (! $this->stripe) {
            throw new \RuntimeException('Stripe is not configured. Set STRIPE_SECRET in .env');
        }

        $intent = $this->stripe->paymentIntents->create([
            'amount' => (int) round($package->total_price * 100),
            'currency' => 'pkr',
            'metadata' => [
                'package_id' => $package->id,
                'vendor_profile_id' => $vendor->id,
            ],
        ]);

        return [
            'client_secret' => $intent->client_secret,
            'intent_id' => $intent->id,
        ];
    }

    public function confirmStripePayment(string $paymentIntentId): array
    {
        if (! $this->stripe) {
            throw new \RuntimeException('Stripe is not configured.');
        }

        $intent = $this->stripe->paymentIntents->retrieve($paymentIntentId);

        return [
            'status' => $intent->status,
            'amount' => $intent->amount / 100,
            'metadata' => $intent->metadata->toArray(),
        ];
    }

    /**
     * Create a purchase row for a card payment, awaiting Stripe confirmation.
     */
    public function createCardPurchase(Package $package, VendorProfile $vendor, string $transactionId): VendorPackagePurchase
    {
        return VendorPackagePurchase::create([
            'vendor_profile_id' => $vendor->id,
            'package_id' => $package->id,
            'amount' => $package->total_price,
            'method' => 'card',
            'status' => 'pending',
            'duration_days' => $package->duration_days,
            'max_halls' => $package->max_halls,
            'max_listings' => $package->max_listings,
            'boost_tier' => $package->boost_tier,
            'transaction_id' => $transactionId,
        ]);
    }

    /**
     * Create a purchase row for a bank transfer (waits for admin approval).
     */
    public function createBankTransferPurchase(Package $package, VendorProfile $vendor, ?string $proofPath = null, ?string $notes = null): VendorPackagePurchase
    {
        return VendorPackagePurchase::create([
            'vendor_profile_id' => $vendor->id,
            'package_id' => $package->id,
            'amount' => $package->total_price,
            'method' => 'bank_transfer',
            'status' => 'pending',
            'duration_days' => $package->duration_days,
            'max_halls' => $package->max_halls,
            'max_listings' => $package->max_listings,
            'boost_tier' => $package->boost_tier,
            'proof_path' => $proofPath,
            'notes' => $notes,
        ]);
    }

    /**
     * Admin manually grants a package (no payment). Activates immediately.
     */
    public function createManualPurchase(Package $package, VendorProfile $vendor, ?string $notes = null): VendorPackagePurchase
    {
        $purchase = VendorPackagePurchase::create([
            'vendor_profile_id' => $vendor->id,
            'package_id' => $package->id,
            'amount' => $package->total_price,
            'method' => 'manual',
            'status' => 'pending',
            'duration_days' => $package->duration_days,
            'max_halls' => $package->max_halls,
            'max_listings' => $package->max_listings,
            'boost_tier' => $package->boost_tier,
            'notes' => $notes,
        ]);

        $this->activate($purchase);

        return $purchase;
    }

    /**
     * Activate a purchase: mark paid, apply the boost, unblock the vendor.
     */
    public function activate(VendorPackagePurchase $purchase): void
    {
        $purchase->update([
            'status' => 'active',
            'paid_at' => now(),
            'starts_at' => now(),
            'ends_at' => now()->addDays($purchase->duration_days ?: 30),
        ]);

        $this->applyBoost($purchase);

        // Unlock the combo template for this purchase.
        if ($purchase->package_id && $purchase->vendor_profile_id) {
            \App\Models\VendorCombo::firstOrCreate([
                'vendor_profile_id' => $purchase->vendor_profile_id,
                'package_purchase_id' => $purchase->id,
                'package_id' => $purchase->package_id,
            ], ['is_active' => true]);
        }
    }

    /**
     * Apply (or refresh) the vendor's boost from the given purchase.
     */
    public function applyBoost(VendorPackagePurchase $purchase): void
    {
        $vendor = $purchase->vendorProfile;
        $vendor->update([
            'status' => 'verified',
            'feature_tier' => $purchase->boost_tier ?? null,
            'featured_until' => $purchase->boost_tier ? $purchase->ends_at : null,
        ]);
    }

    /**
     * Recompute the vendor's boost from their currently active purchases.
     * Used when a package expires so older boosts can still be honoured.
     */
    public function refreshBoost(VendorProfile $vendor): void
    {
        $active = $vendor->packagePurchases()
            ->where('status', 'active')
            ->where('ends_at', '>', now())
            ->get();

        if ($active->isEmpty()) {
            $vendor->update(['feature_tier' => null, 'featured_until' => null]);

            return;
        }

        $best = $active->sortByDesc(function ($p) {
            $weight = $p->boost_tier === 'premium' ? 2 : ($p->boost_tier === 'featured' ? 1 : 0);

            return [$weight, optional($p->ends_at)->timestamp ?? 0];
        })->first();

        $vendor->update([
            'feature_tier' => $best->boost_tier ?? null,
            'featured_until' => $best->boost_tier ? $best->ends_at : null,
        ]);
    }

    public function expire(VendorPackagePurchase $purchase): void
    {
        $purchase->update(['status' => 'expired']);

        if ($purchase->vendorProfile) {
            $this->refreshBoost($purchase->vendorProfile);
        }
    }
}
