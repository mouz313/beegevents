<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\PackageItem;
use App\Models\ServiceCategory;
use App\Models\HallUnit;
use App\Models\ServiceListing;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::with('packageItems')->get();
        $categories = ServiceCategory::all();
        $hallUnits = HallUnit::with('hall')->get();
        $listings = ServiceListing::with('serviceCategory', 'vendorProfile')->get();
        return view('admin.packages.index', compact('packages', 'categories', 'hallUnits', 'listings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'total_price' => 'required|numeric|min:0',
            'event_type' => 'required|in:wedding,engagement,corporate,birthday,home,other',
            'items' => 'nullable|array',
            'items.*.type' => 'required|in:hall_unit,service_listing',
            'items.*.id' => 'required|integer',
        ]);

        $package = Package::create($request->only('title', 'description', 'total_price', 'event_type'));

        if ($request->has('items')) {
            foreach ($request->items as $item) {
                $itemableType = $item['type'] === 'hall_unit' ? 'App\Models\HallUnit' : 'App\Models\ServiceListing';
                PackageItem::create([
                    'package_id' => $package->id,
                    'itemable_type' => $itemableType,
                    'itemable_id' => $item['id'],
                ]);
            }
        }

        if ($request->ajax()) return response()->json(['success' => true, 'package' => $package]);
        return redirect()->back()->with('success', 'Package created!');
    }

    public function show(Package $package)
    {
        $package->load('packageItems');
        return view('admin.packages.show', compact('package'));
    }

    public function edit(Package $package)
    {
        $categories = ServiceCategory::all();
        $hallUnits = HallUnit::with('hall')->get();
        $listings = ServiceListing::with('serviceCategory', 'vendorProfile')->get();
        $package->load('packageItems');
        return view('admin.packages.edit', compact('package', 'categories', 'hallUnits', 'listings'));
    }

    public function update(Request $request, Package $package)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'total_price' => 'required|numeric|min:0',
            'event_type' => 'required|in:wedding,engagement,corporate,birthday,home,other',
            'items' => 'nullable|array',
            'items.*.type' => 'required|in:hall_unit,service_listing',
            'items.*.id' => 'required|integer',
        ]);

        $package->update($request->only('title', 'description', 'total_price', 'event_type'));

        $package->packageItems()->delete();
        if ($request->has('items')) {
            foreach ($request->items as $item) {
                $itemableType = $item['type'] === 'hall_unit' ? 'App\Models\HallUnit' : 'App\Models\ServiceListing';
                PackageItem::create([
                    'package_id' => $package->id,
                    'itemable_type' => $itemableType,
                    'itemable_id' => $item['id'],
                ]);
            }
        }

        if ($request->ajax()) return response()->json(['success' => true]);
        return redirect()->route('admin.packages.index')->with('success', 'Package updated!');
    }

    public function destroy(Package $package)
    {
        $package->delete();
        if (request()->ajax()) return response()->json(['success' => true]);
        return redirect()->back()->with('success', 'Package deleted!');
    }
}
