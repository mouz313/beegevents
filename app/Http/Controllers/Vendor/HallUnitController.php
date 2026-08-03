<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\HallUnit;
use App\Models\Hall;
use App\Models\Floor;
use Illuminate\Http\Request;

class HallUnitController extends Controller
{
    public function index(Hall $hall)
    {
        $floors = $hall->floors;
        $units = $hall->hallUnits()->with('floor')->get();
        if (request()->ajax()) {
            return response()->json(['units' => $units]);
        }
        return view('vendor.halls.units', compact('hall', 'floors', 'units'));
    }

    public function store(Request $request, Hall $hall)
    {
        $request->validate([
            'unit_name' => 'required|string|max:255',
            'floor_id' => 'nullable|exists:floors,id',
            'min_capacity' => 'required|integer|min:1',
            'max_capacity' => 'required|integer|min:1|gte:min_capacity',
            'menu_summary' => 'nullable|string',
            'decor_type' => 'required|in:fixed,outsourced,customizable',
            'catering_mode' => 'nullable|in:internal,external,both,none',
            'food_service_style' => 'nullable|in:static_place,on_table,both',
            'staff_male' => 'nullable|integer|min:0',
            'staff_female' => 'nullable|integer|min:0',
            'amenities' => 'nullable|array',
            'amenities.*' => 'string',
            'base_price' => 'required|numeric|min:0',
        ]);

        $data = $request->all();
        $data['amenities'] = $request->input('amenities', []);
        $unit = $hall->hallUnits()->create($data);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'unit' => $unit->load('floor')]);
        }
        return redirect()->back()->with('success', 'Hall unit created!');
    }

    public function update(Request $request, HallUnit $hallUnit)
    {
        $request->validate([
            'unit_name' => 'required|string|max:255',
            'floor_id' => 'nullable|exists:floors,id',
            'min_capacity' => 'required|integer|min:1',
            'max_capacity' => 'required|integer|min:1|gte:min_capacity',
            'menu_summary' => 'nullable|string',
            'decor_type' => 'required|in:fixed,outsourced,customizable',
            'catering_mode' => 'nullable|in:internal,external,both,none',
            'food_service_style' => 'nullable|in:static_place,on_table,both',
            'staff_male' => 'nullable|integer|min:0',
            'staff_female' => 'nullable|integer|min:0',
            'amenities' => 'nullable|array',
            'amenities.*' => 'string',
            'base_price' => 'required|numeric|min:0',
        ]);

        $data = $request->all();
        $data['amenities'] = $request->input('amenities', []);
        $hallUnit->update($data);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'unit' => $hallUnit->load('floor')]);
        }
        return redirect()->back()->with('success', 'Hall unit updated!');
    }

    public function destroy(HallUnit $hallUnit)
    {
        $hallUnit->delete();
        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }
        return redirect()->back()->with('success', 'Hall unit deleted!');
    }
}
