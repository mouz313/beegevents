<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\ServiceListing;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;

class ServiceListingController extends Controller
{
    public function index()
    {
        $profile = auth()->user()->vendorProfile;
        $categories = ServiceCategory::all();
        $listings = $profile ? $profile->serviceListings()->with('serviceCategory')->get() : collect();
        return view('vendor.listings.index', compact('profile', 'categories', 'listings'));
    }

    public function store(Request $request)
    {
        $profile = auth()->user()->vendorProfile;
        $request->validate([
            'service_category_id' => 'required|exists:service_categories,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'price_unit' => 'required|in:fixed,per_person,per_hour',
        ]);

        $listing = $profile->serviceListings()->create($request->all());

        if ($request->ajax()) {
            return response()->json(['success' => true, 'listing' => $listing->load('category')]);
        }
        return redirect()->back()->with('success', 'Service listing created!');
    }

    public function update(Request $request, ServiceListing $serviceListing)
    {
        $request->validate([
            'service_category_id' => 'required|exists:service_categories,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'price_unit' => 'required|in:fixed,per_person,per_hour',
        ]);

        $serviceListing->update($request->all());

        if ($request->ajax()) {
            return response()->json(['success' => true, 'listing' => $serviceListing->load('category')]);
        }
        return redirect()->back()->with('success', 'Service listing updated!');
    }

    public function destroy(ServiceListing $serviceListing)
    {
        $serviceListing->delete();
        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }
        return redirect()->back()->with('success', 'Service listing deleted!');
    }
}
