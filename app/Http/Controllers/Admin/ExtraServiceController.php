<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExtraService;
use App\Models\Hall;
use App\Models\HallUnit;
use Illuminate\Http\Request;

class ExtraServiceController extends Controller
{
    public function index(Hall $hall, HallUnit $unit)
    {
        if ($unit->hall_id !== $hall->id) abort(404);
        $services = $unit->extraServices;
        return response()->json(['services' => $services]);
    }

    public function store(Request $request, Hall $hall, HallUnit $unit)
    {
        if ($unit->hall_id !== $hall->id) abort(404);

        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'price_unit' => 'required|string|max:50',
        ]);

        $service = ExtraService::create([
            'serviceable_type' => 'App\Models\HallUnit',
            'serviceable_id' => $unit->id,
            'name' => $request->name,
            'price' => $request->price,
            'price_unit' => $request->price_unit,
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'service' => $service]);
        }
        return redirect()->back()->with('success', 'Extra service added!');
    }

    public function destroy(Request $request, ExtraService $extraService)
    {
        $extraService->delete();

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }
        return redirect()->back()->with('success', 'Extra service removed.');
    }
}
