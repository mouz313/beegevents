<?php

namespace App\Http\Controllers;

use App\Models\Inquiry;
use App\Models\User;
use App\Mail\InquiryNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class InquiryController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'vendor_profile_id' => 'required|exists:vendor_profiles,id',
            'inquiriable_type' => 'required|in:App\Models\Hall,App\Models\ServiceListing',
            'inquiriable_id' => 'required|integer',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'message' => 'required|string|max:2000',
        ]);

        $inquiry = Inquiry::create([
            'vendor_profile_id' => $request->vendor_profile_id,
            'user_id' => auth()->id(),
            'inquiriable_type' => $request->inquiriable_type,
            'inquiriable_id' => $request->inquiriable_id,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'message' => $request->message,
            'status' => 'pending',
        ]);

        try {
            $vendorUser = $inquiry->vendorProfile->user;
            if ($vendorUser && $vendorUser->email) {
                Mail::to($vendorUser->email)->send(new InquiryNotification($inquiry, 'vendor'));
            }

            $admin = User::where('role', 'admin')->first();
            if ($admin && $admin->email) {
                Mail::to($admin->email)->send(new InquiryNotification($inquiry, 'admin'));
            }

            Mail::to($inquiry->email)->send(new InquiryNotification($inquiry, 'customer'));
        } catch (\Exception $e) {
            // Email failure shouldn't break the inquiry submission
        }

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Inquiry sent! Vendor will contact you shortly.']);
        }

        return redirect()->back()->with('success', 'Inquiry sent! Vendor will contact you shortly.');
    }
}
