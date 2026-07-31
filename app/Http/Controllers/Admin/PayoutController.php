<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payout;
use App\Models\VendorProfile;
use App\Services\PayoutService;
use Illuminate\Http\Request;

class PayoutController extends Controller
{
    protected PayoutService $payoutService;

    public function __construct(PayoutService $payoutService)
    {
        $this->payoutService = $payoutService;
    }

    public function index()
    {
        $vendors = VendorProfile::with('user')
            ->where('status', 'verified')
            ->get()
            ->each(function ($vendor) {
                $vendor->payout_due = $this->payoutService->calculateDue($vendor);
            });

        $payouts = Payout::with('vendorProfile.user')->latest()->paginate(20);

        return view('admin.payouts.index', compact('vendors', 'payouts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'vendor_profile_id' => 'required|exists:vendor_profiles,id',
            'amount' => 'required|numeric|min:1',
            'notes' => 'nullable|string|max:1000',
        ]);

        Payout::create([
            'vendor_profile_id' => $request->vendor_profile_id,
            'amount' => $request->amount,
            'method' => 'bank_transfer',
            'status' => 'pending',
            'notes' => $request->notes,
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Payout recorded.');
    }

    public function markProcessed(Request $request, Payout $payout)
    {
        if ($payout->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Payout is not pending.'], 422);
        }

        $payout->update(['status' => 'processed', 'processed_at' => now()]);

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Payout marked as processed.');
    }
}
