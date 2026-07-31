<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\PackageItem;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index()
    {
        $profile = auth()->user()->vendorProfile;

        $packages = Package::with('packageItems')
            ->where('vendor_profile_id', $profile->id)
            ->latest()
            ->get();

        $hallUnits = $profile->halls()->with('hallUnits')->get()
            ->pluck('hallUnits')
            ->flatten()
            ->each(function ($unit) {
                $unit->label = $unit->hall->name.' - '.$unit->unit_name;
            });

        $listings = $profile->serviceListings()->with('serviceCategory')->get();

        return view('vendor.packages.index', compact('packages', 'hallUnits', 'listings'));
    }

    public function store(Request $request)
    {
        $profile = auth()->user()->vendorProfile;

        $validated = $this->validatePackage($request);

        $package = Package::create(array_merge(
            $request->only('title', 'description', 'total_price', 'event_type'),
            ['vendor_profile_id' => $profile->id]
        ));

        $this->syncItems($package, $request);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'package' => $package]);
        }

        return redirect()->route('vendor.packages.index')->with('success', 'Package created!');
    }

    public function update(Request $request, Package $package)
    {
        $this->authorizePackage($package);

        $this->validatePackage($request);

        $package->update($request->only('title', 'description', 'total_price', 'event_type'));

        $package->packageItems()->delete();
        $this->syncItems($package, $request);

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('vendor.packages.index')->with('success', 'Package updated!');
    }

    public function destroy(Package $package)
    {
        $this->authorizePackage($package);

        $package->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('vendor.packages.index')->with('success', 'Package deleted!');
    }

    protected function validatePackage(Request $request): array
    {
        return $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'total_price' => 'required|numeric|min:0',
            'event_type' => 'required|in:wedding,engagement,corporate,birthday,home,other',
            'items' => 'nullable|array',
            'items.*.type' => 'required|in:hall_unit,service_listing',
            'items.*.id' => 'required|integer',
        ]);
    }

    protected function syncItems(Package $package, Request $request): void
    {
        $profile = auth()->user()->vendorProfile;
        $ownUnitIds = $profile->halls()->with('hallUnits')->get()
            ->pluck('hallUnits')->flatten()->pluck('id')->all();
        $ownListingIds = $profile->serviceListings()->pluck('id')->all();

        if (! $request->has('items')) {
            return;
        }

        foreach ($request->items as $item) {
            if ($item['type'] === 'hall_unit' && ! in_array((int) $item['id'], $ownUnitIds)) {
                continue;
            }
            if ($item['type'] === 'service_listing' && ! in_array((int) $item['id'], $ownListingIds)) {
                continue;
            }

            $itemableType = $item['type'] === 'hall_unit' ? 'App\Models\HallUnit' : 'App\Models\ServiceListing';
            PackageItem::create([
                'package_id' => $package->id,
                'itemable_type' => $itemableType,
                'itemable_id' => $item['id'],
            ]);
        }
    }

    protected function authorizePackage(Package $package): void
    {
        if ($package->vendor_profile_id !== auth()->user()->vendorProfile->id) {
            abort(403);
        }
    }
}
