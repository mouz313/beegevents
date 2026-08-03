<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\VendorPackagePurchase;
use App\Models\VendorProfile;
use App\Services\VendorPackagePurchaseService;
use Illuminate\Http\Request;

class PackagePurchaseController extends Controller
{
    public function index(Request $request)
    {
        $query = VendorPackagePurchase::with(['vendorProfile.user', 'package']);

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $purchases = $query->latest()->paginate(15)->withQueryString();

        return view('admin.package-purchases.index', compact('purchases'));
    }

    public function show(VendorPackagePurchase $vendorPackagePurchase)
    {
        $vendorPackagePurchase->load('vendorProfile.user', 'package.packageItems');

        return view('admin.package-purchases.show', compact('vendorPackagePurchase'));
    }

    public function approve(VendorPackagePurchase $vendorPackagePurchase, VendorPackagePurchaseService $service)
    {
        if ($vendorPackagePurchase->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'This purchase is already processed.'], 422);
        }

        $service->activate($vendorPackagePurchase);

        return response()->json(['success' => true]);
    }

    public function storeManual(Request $request, VendorPackagePurchaseService $service)
    {
        $request->validate([
            'vendor_profile_id' => 'required|exists:vendor_profiles,id',
            'package_id' => 'required|exists:packages,id',
            'notes' => 'nullable|string|max:500',
        ]);

        $vendor = VendorProfile::findOrFail($request->vendor_profile_id);
        $package = Package::findOrFail($request->package_id);

        $service->createManualPurchase($package, $vendor, $request->notes);

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Package granted to vendor.');
    }
}
