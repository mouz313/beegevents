<?php

namespace App\Http\Controllers\Vendor;

use App\Helpers\ImageHelper;
use App\Http\Controllers\Controller;
use App\Models\VendorProfile;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index()
    {
        $profile = auth()->user()->vendorProfile;
        return view('vendor.profile', compact('profile'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'business_name' => 'required|string|max:255',
            'vendor_type' => 'required|in:hall,farmhouse,decor,catering,photography,dj,car,other',
            'city' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'cancellation_policy' => 'nullable|string',
            'cancel_free_days' => 'nullable|integer|min:0',
            'cancel_refund_percent' => 'nullable|numeric|min:0|max:100',
            'bank_name' => 'nullable|string|max:100',
            'bank_account_title' => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_iban' => 'nullable|string|max:50',
            'cnic_front' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'cnic_back' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
        ]);

        $data = [
            'user_id' => auth()->id(),
            'business_name' => $request->business_name,
            'vendor_type' => $request->vendor_type,
            'city' => $request->city,
            'phone' => $request->phone,
            'address' => $request->address,
            'cancellation_policy' => $request->cancellation_policy,
            'cancel_free_days' => $request->cancel_free_days,
            'cancel_refund_percent' => $request->cancel_refund_percent,
            'bank_name' => $request->bank_name,
            'bank_account_title' => $request->bank_account_title,
            'bank_account_number' => $request->bank_account_number,
            'bank_iban' => $request->bank_iban,
        ];

        if ($request->hasFile('logo')) {
            $data['logo_path'] = ImageHelper::uploadAndCompress($request->file('logo'), 'vendor-logos');
        }
        if ($request->hasFile('cnic_front')) {
            $data['cnic_front_path'] = ImageHelper::uploadAndCompress($request->file('cnic_front'), 'vendor-cnic');
        }
        if ($request->hasFile('cnic_back')) {
            $data['cnic_back_path'] = ImageHelper::uploadAndCompress($request->file('cnic_back'), 'vendor-cnic');
        }

        $profile = VendorProfile::create($data);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'profile' => $profile]);
        }

        return redirect()->route('vendor.dashboard')->with('success', 'Profile created!');
    }

    public function update(Request $request, VendorProfile $vendorProfile)
    {
        $this->authorize('update', $vendorProfile);

        $request->validate([
            'business_name' => 'required|string|max:255',
            'vendor_type' => 'required|in:hall,farmhouse,decor,catering,photography,dj,car,other',
            'city' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'cancellation_policy' => 'nullable|string',
            'cancel_free_days' => 'nullable|integer|min:0',
            'cancel_refund_percent' => 'nullable|numeric|min:0|max:100',
            'bank_name' => 'nullable|string|max:100',
            'bank_account_title' => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_iban' => 'nullable|string|max:50',
            'cnic_front' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'cnic_back' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
        ]);

        $data = $request->only('business_name', 'vendor_type', 'city', 'phone', 'address', 'cancellation_policy', 'cancel_free_days', 'cancel_refund_percent', 'bank_name', 'bank_account_title', 'bank_account_number', 'bank_iban');

        if ($request->hasFile('logo')) {
            $data['logo_path'] = ImageHelper::uploadAndCompress($request->file('logo'), 'vendor-logos');
        }
        if ($request->hasFile('cnic_front')) {
            $data['cnic_front_path'] = ImageHelper::uploadAndCompress($request->file('cnic_front'), 'vendor-cnic');
        }
        if ($request->hasFile('cnic_back')) {
            $data['cnic_back_path'] = ImageHelper::uploadAndCompress($request->file('cnic_back'), 'vendor-cnic');
        }

        $vendorProfile->update($data);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'profile' => $vendorProfile]);
        }

        return redirect()->route('vendor.dashboard')->with('success', 'Profile updated!');
    }

    public function completeOnboarding(Request $request)
    {
        $profile = auth()->user()->vendorProfile;
        if (!$profile) {
            return redirect()->route('vendor.profile.create');
        }
        $profile->update(['onboarding_completed' => true]);
        return redirect()->route('vendor.dashboard')->with('success', 'Onboarding complete! Welcome to BeeG Events.');
    }
}
