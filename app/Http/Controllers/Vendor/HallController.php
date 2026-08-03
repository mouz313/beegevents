<?php

namespace App\Http\Controllers\Vendor;

use App\Helpers\ImageHelper;
use App\Http\Controllers\Controller;
use App\Models\Hall;
use App\Models\HallImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HallController extends Controller
{
    public function index()
    {
        $profile = auth()->user()->vendorProfile;
        if (!$profile || !in_array($profile->vendor_type, ['hall', 'farmhouse'])) {
            return redirect()->route('vendor.dashboard')->with('error', 'Only hall/farmhouse vendors can manage halls.');
        }
        $halls = $profile->halls()->with('floors', 'hallUnits', 'hallImages')->get();
        return view('vendor.halls.index', compact('halls', 'profile'));
    }

    public function store(Request $request)
    {
        $profile = auth()->user()->vendorProfile;
        if (!$profile->hasHallSlot()) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'error' => 'Hall limit reached for your current plan. Upgrade your package to add more halls.'], 422);
            }
            return redirect()->route('vendor.halls.index')->with('error', 'Hall limit reached for your current plan. Upgrade your package to add more halls.');
        }
        $request->validate([
            'name' => 'required|string|max:255',
            'venue_type' => 'nullable|in:marriage_hall,banquet_hall,farm_house,community_center,hotel_ballroom,rooftop,lawn,marquee',
            'address' => 'required|string',
            'description' => 'nullable|string',
            'has_floors' => 'boolean',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
        ]);

        $hall = $profile->halls()->create($request->all());

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $i => $file) {
                $path = ImageHelper::uploadAndCompress($file, 'hall-images');
                $hall->hallImages()->create([
                    'image_path' => $path,
                    'sort_order' => $i,
                ]);
            }
        }

        if ($request->ajax()) {
            return response()->json(['success' => true, 'hall' => $hall]);
        }
        return redirect()->route('vendor.halls.index')->with('success', 'Hall created!');
    }

    public function update(Request $request, Hall $hall)
    {
        $hall->update($request->validate([
            'name' => 'required|string|max:255',
            'venue_type' => 'nullable|in:marriage_hall,banquet_hall,farm_house,community_center,hotel_ballroom,rooftop,lawn,marquee',
            'address' => 'required|string',
            'description' => 'nullable|string',
            'has_floors' => 'boolean',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
        ]));

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $i => $file) {
                $path = ImageHelper::uploadAndCompress($file, 'hall-images');
                $hall->hallImages()->create([
                    'image_path' => $path,
                    'sort_order' => $hall->hallImages->count() + $i,
                ]);
            }
        }

        if ($request->ajax()) {
            return response()->json(['success' => true, 'hall' => $hall]);
        }
        return redirect()->route('vendor.halls.index')->with('success', 'Hall updated!');
    }

    public function destroy(Hall $hall)
    {
        foreach ($hall->hallImages as $img) {
            Storage::disk('public')->delete($img->image_path);
            $img->delete();
        }
        $hall->delete();
        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }
        return redirect()->route('vendor.halls.index')->with('success', 'Hall deleted!');
    }

    public function uploadImage(Request $request, Hall $hall)
    {
        $request->validate(['image' => 'required|image|mimes:jpeg,png,jpg,webp|max:10240']);

        $path = ImageHelper::uploadAndCompress($request->file('image'), 'hall-images');
        $maxSort = $hall->hallImages()->max('sort_order') ?? 0;

        $img = $hall->hallImages()->create([
            'image_path' => $path,
            'sort_order' => $maxSort + 1,
            'caption' => $request->caption,
        ]);

        return response()->json(['success' => true, 'image' => $img]);
    }

    public function deleteImage(HallImage $hallImage)
    {
        Storage::disk('public')->delete($hallImage->image_path);
        $hallImage->delete();
        return response()->json(['success' => true]);
    }
}
