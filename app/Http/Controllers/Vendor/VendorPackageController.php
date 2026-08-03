<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Services\VendorPackagePurchaseService;
use Illuminate\Http\Request;

class VendorPackageController extends Controller
{
    public function index()
    {
        $vendor = auth()->user()->vendorProfile;

        $plans = Package::where('is_active', true)
            ->with('packageItems')
            ->orderBy('total_price')
            ->get();

        $current = $vendor?->activePackage();
        $purchases = $vendor
            ? $vendor->packagePurchases()->with('package')->latest()->get()
            : collect();

        $status = $vendor?->status;
        $blocked = $status === 'blocked';
        $onTrial = $vendor?->onTrial() ?? false;

        return view('vendor.packages.index', compact(
            'plans', 'current', 'purchases', 'vendor', 'blocked', 'onTrial', 'status'
        ));
    }

    public function checkout(Package $package)
    {
        if (! $package->is_active) {
            abort(404);
        }

        $vendor = auth()->user()->vendorProfile;
        if (! $vendor) {
            return redirect()->route('vendor.profile.create')->with('warning', 'Please complete your business profile first.');
        }

        $stripeKey = config('services.stripe.key');

        return view('vendor.packages.checkout', compact('package', 'vendor', 'stripeKey'));
    }

    public function createIntent(Package $package, VendorPackagePurchaseService $service)
    {
        if (! $package->is_active) {
            abort(404);
        }

        $vendor = auth()->user()->vendorProfile;
        if (! $vendor) {
            return response()->json(['success' => false, 'message' => 'Complete your business profile first.'], 422);
        }

        try {
            $intent = $service->createStripePaymentIntent($package, $vendor);

            return response()->json([
                'success' => true,
                'client_secret' => $intent['client_secret'],
                'intent_id' => $intent['intent_id'],
                'amount' => $package->total_price,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function confirm(Request $request, Package $package, VendorPackagePurchaseService $service)
    {
        if (! $package->is_active) {
            abort(404);
        }

        $request->validate(['payment_intent_id' => 'required|string']);

        $vendor = auth()->user()->vendorProfile;
        if (! $vendor) {
            return response()->json(['success' => false, 'message' => 'Complete your business profile first.'], 422);
        }

        try {
            $result = $service->confirmStripePayment($request->payment_intent_id);

            $metadata = $result['metadata'] ?? [];
            if (($metadata['package_id'] ?? null) != $package->id
                || ($metadata['vendor_profile_id'] ?? null) != $vendor->id) {
                return response()->json(['success' => false, 'message' => 'This payment does not belong to this package.'], 403);
            }

            if (round((float) $result['amount'], 2) !== round($package->total_price, 2)) {
                return response()->json(['success' => false, 'message' => 'Payment amount does not match the package price.'], 422);
            }

            if ($result['status'] === 'succeeded') {
                $purchase = $service->createCardPurchase($package, $vendor, $request->payment_intent_id);
                $service->activate($purchase);

                return response()->json(['success' => true]);
            }

            return response()->json(['success' => false, 'message' => 'Payment not completed.'], 400);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function manual(Request $request, Package $package, VendorPackagePurchaseService $service)
    {
        if (! $package->is_active) {
            abort(404);
        }

        $request->validate([
            'proof' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'notes' => 'nullable|string|max:500',
        ]);

        $vendor = auth()->user()->vendorProfile;
        if (! $vendor) {
            return redirect()->route('vendor.profile.create')->with('warning', 'Please complete your business profile first.');
        }

        $proofPath = null;
        if ($request->hasFile('proof')) {
            $proofPath = $request->file('proof')->store('package-proofs', 'public');
        }

        $service->createBankTransferPurchase($package, $vendor, $proofPath, $request->notes);

        return redirect()->route('vendor.packages.index')
            ->with('success', 'Payment details submitted. Admin will activate your package after verification.');
    }
}
