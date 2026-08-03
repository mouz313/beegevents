<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ImageHelper;
use App\Http\Controllers\Controller;
use App\Models\Floor;
use App\Models\Hall;
use App\Models\HallImage;
use App\Models\HallUnit;
use App\Models\VendorProfile;
use App\Services\VendorSpecService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VendorVerificationController extends Controller
{
    public function pending()
    {
        $vendors = VendorProfile::where('status', 'pending')->with('user')->get();
        return view('admin.vendors.pending', compact('vendors'));
    }

    public function index(Request $request)
    {
        $query = VendorProfile::with('user');

        if ($request->get('kyc') === 'incomplete') {
            $query->incompleteKyc();
        }

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('business_name', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('vendor_type', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        $vendors = $query->latest()->paginate(10)->withQueryString();
        return view('admin.vendors.index', compact('vendors'));
    }

    public function show(VendorProfile $vendorProfile)
    {
        $vendorProfile->load('user', 'halls.floors.hallUnits', 'halls.hallImages', 'serviceListings');
        return view('admin.vendors.show', compact('vendorProfile'));
    }

    public function edit(VendorProfile $vendorProfile)
    {
        $vendorProfile->load('halls.floors.hallUnits', 'halls.hallImages');
        return view('admin.vendors.edit', compact('vendorProfile'));
    }

    public function update(Request $request, VendorProfile $vendorProfile)
    {
        $request->validate(array_merge([
            'business_name' => 'required|string|max:255',
            'vendor_type' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'status' => 'required|in:pending,verified,suspended,blocked',
            'cancellation_policy' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
        ], VendorSpecService::rulesFor($request->vendor_type)));

        $data = $request->only('business_name', 'vendor_type', 'city', 'phone', 'address', 'status', 'cancellation_policy');

        if ($request->hasFile('logo')) {
            $data['logo_path'] = ImageHelper::uploadAndCompress($request->file('logo'), 'vendor-logos');
        }

        $vendorProfile->update($data);

        VendorSpecService::save($vendorProfile, $request);

        if ($request->ajax()) return response()->json(['success' => true]);
        return redirect()->route('admin.vendors.index')->with('success', 'Vendor updated successfully.');
    }

    public function destroy(VendorProfile $vendorProfile)
    {
        $vendorProfile->delete();
        if (request()->ajax()) return response()->json(['success' => true]);
        return redirect()->route('admin.vendors.index')->with('success', 'Vendor deleted.');
    }

    public function verify(VendorProfile $vendorProfile)
    {
        if (!$vendorProfile->kycComplete()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot verify — vendor KYC is incomplete: ' . implode(', ', $vendorProfile->kycMissing()) . '.',
            ], 422);
        }

        $vendorProfile->update([
            'status' => 'verified',
            'trial_ends_at' => $vendorProfile->trial_ends_at ?? now()->addDays(3),
        ]);
        return response()->json(['success' => true]);
    }

    public function suspend(VendorProfile $vendorProfile)
    {
        $vendorProfile->update(['status' => 'suspended']);
        return response()->json(['success' => true]);
    }

    public function unblock(VendorProfile $vendorProfile)
    {
        $vendorProfile->update([
            'status' => 'verified',
            'trial_ends_at' => now()->addDays(3),
        ]);

        return response()->json(['success' => true]);
    }

    public function uploadHallImage(Request $request, Hall $hall)
    {
        $request->validate(['image' => 'required|image|mimes:jpeg,png,jpg,webp|max:10240']);

        $path = ImageHelper::uploadAndCompress($request->file('image'), 'hall-images');
        $maxSort = $hall->hallImages()->max('sort_order') ?? 0;

        $img = $hall->hallImages()->create([
            'image_path' => $path,
            'sort_order' => $maxSort + 1,
        ]);

        return response()->json(['success' => true, 'image' => $img]);
    }

    public function deleteHallImage(HallImage $hallImage)
    {
        Storage::disk('public')->delete($hallImage->image_path);
        $hallImage->delete();
        return response()->json(['success' => true]);
    }

    public function storeFloor(Request $request, Hall $hall)
    {
        $request->validate(['floor_label' => 'required|string|max:255']);

        $floor = $hall->floors()->create(['floor_label' => $request->floor_label]);

        return response()->json(['success' => true, 'floor' => [
            'id' => $floor->id,
            'floor_label' => $floor->floor_label,
            'hall_id' => $floor->hall_id,
            'units' => [],
            'units_count' => 0,
        ]]);
    }

    public function deleteFloor(Floor $floor)
    {
        $floor->hallUnits()->delete();
        $floor->delete();
        return response()->json(['success' => true]);
    }

    public function storeUnit(Request $request, Hall $hall)
    {
        $request->validate([
            'floor_id' => 'required|exists:floors,id',
            'unit_name' => 'required|string|max:255',
            'min_capacity' => 'nullable|integer|min:0',
            'max_capacity' => 'nullable|integer|min:0',
            'base_price' => 'nullable|numeric|min:0',
        ]);

        $floor = Floor::findOrFail($request->floor_id);
        if ($floor->hall_id !== $hall->id) {
            return response()->json(['success' => false, 'message' => 'Floor does not belong to this hall.'], 422);
        }

        $unit = $hall->hallUnits()->create($request->only('floor_id', 'unit_name', 'min_capacity', 'max_capacity', 'base_price'));

        return response()->json(['success' => true, 'unit' => $unit]);
    }

    public function deleteUnit(HallUnit $hallUnit)
    {
        $hallUnit->delete();
        return response()->json(['success' => true]);
    }
}
