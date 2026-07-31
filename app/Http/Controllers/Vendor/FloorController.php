<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Floor;
use App\Models\Hall;
use Illuminate\Http\Request;

class FloorController extends Controller
{
    public function index(Hall $hall)
    {
        $floors = $hall->floors;
        if (request()->ajax()) {
            return response()->json(['floors' => $floors]);
        }
        return view('vendor.halls.floors', compact('hall', 'floors'));
    }

    public function store(Request $request, Hall $hall)
    {
        $request->validate([
            'floor_label' => 'required|string|max:255',
        ]);

        $floor = $hall->floors()->create($request->all());

        if ($request->ajax()) {
            return response()->json(['success' => true, 'floor' => $floor]);
        }
        return redirect()->back()->with('success', 'Floor added!');
    }

    public function update(Request $request, Floor $floor)
    {
        $request->validate(['floor_label' => 'required|string|max:255']);
        $floor->update($request->all());

        if ($request->ajax()) {
            return response()->json(['success' => true, 'floor' => $floor]);
        }
        return redirect()->back()->with('success', 'Floor updated!');
    }

    public function destroy(Floor $floor)
    {
        $floor->delete();
        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }
        return redirect()->back()->with('success', 'Floor deleted!');
    }
}
