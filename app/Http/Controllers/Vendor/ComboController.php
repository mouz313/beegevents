<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\HallUnit;
use App\Models\ServiceListing;
use App\Models\VendorCombo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ComboController extends Controller
{
    public function index()
    {
        $vendor = auth()->user()->vendorProfile;
        $combos = $vendor
            ? $vendor->combos()->with(['package.packageItems.itemable', 'items'])->get()
            : collect();

        return view('vendor.combos.index', compact('vendor', 'combos'));
    }

    public function save(Request $request, VendorCombo $combo)
    {
        $vendor = auth()->user()->vendorProfile;
        abort_unless($vendor && $combo->vendor_profile_id === $vendor->id, 403);

        $selections = $request->input('items', []);

        DB::transaction(function () use ($combo, $selections, $vendor) {
            $combo->items()->delete();

            foreach ($combo->templateSlots() as $index => $slot) {
                $id = $selections[$index] ?? null;
                if (! $id) {
                    continue;
                }

                if ($slot->itemable_type === 'App\Models\HallUnit') {
                    $itemable = HallUnit::with('hall')->find($id);
                    if (! $itemable || $itemable->hall?->vendor_profile_id !== $vendor->id) {
                        continue;
                    }
                } else {
                    $itemable = ServiceListing::find($id);
                    if (! $itemable || $itemable->vendor_profile_id !== $vendor->id) {
                        continue;
                    }
                }

                $combo->items()->create([
                    'slot_index' => $index,
                    'itemable_type' => $slot->itemable_type,
                    'itemable_id' => $itemable->id,
                ]);
            }
        });

        return redirect()->route('vendor.combos.index')->with('success', 'Combo updated!');
    }
}
