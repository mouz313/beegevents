<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use Illuminate\Http\Request;

class InquiryController extends Controller
{
    public function index()
    {
        $profile = auth()->user()->vendorProfile;
        if (!$profile) return redirect()->route('vendor.dashboard');

        $inquiries = Inquiry::where('vendor_profile_id', $profile->id)
            ->with('inquiriable', 'user')
            ->latest()
            ->paginate(20);

        return view('vendor.inquiries.index', compact('inquiries'));
    }

    public function updateStatus(Request $request, Inquiry $inquiry)
    {
        $profile = auth()->user()->vendorProfile;
        if (!$profile || $inquiry->vendor_profile_id !== $profile->id) {
            abort(403);
        }

        $request->validate(['status' => 'required|in:replied,closed']);

        $inquiry->update(['status' => $request->status]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Inquiry ' . $request->status . '!']);
        }

        return redirect()->back()->with('success', 'Inquiry marked as ' . $request->status . '!');
    }
}
