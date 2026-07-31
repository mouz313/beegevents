<?php

namespace App\Http\Controllers\Vendor;

use App\Helpers\ImageHelper;
use App\Http\Controllers\Controller;
use App\Models\VendorProfile;
use Illuminate\Http\Request;

class OnboardingController extends Controller
{
    public function index()
    {
        $profile = auth()->user()->vendorProfile;

        if (!$profile) {
            return redirect()->route('vendor.profile.create');
        }

        if ($profile->onboarding_completed) {
            return redirect()->route('vendor.dashboard');
        }

        $step = 1;
        if ($profile->business_name && $profile->vendor_type) {
            $step = 2;
        }
        if ($step == 2 && ($profile->bank_name || $profile->cnic_front_path)) {
            $step = 3;
        }
        if ($step == 3 && $profile->onboarding_completed) {
            $step = 4;
        }

        return view('vendor.onboarding.index', compact('profile', 'step'));
    }

    public function step1(Request $request)
    {
        $profile = auth()->user()->vendorProfile;
        if (!$profile) {
            return redirect()->route('vendor.profile.create');
        }

        $request->validate([
            'business_name' => 'required|string|max:255',
            'vendor_type' => 'required|in:hall,farmhouse,decor,catering,photography,dj,car,other',
            'city' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'cancellation_policy' => 'nullable|string',
        ]);

        $profile->update($request->only('business_name', 'vendor_type', 'city', 'phone', 'address', 'cancellation_policy'));

        return redirect()->route('vendor.onboarding');
    }

    public function step2(Request $request)
    {
        $profile = auth()->user()->vendorProfile;
        if (!$profile) {
            return redirect()->route('vendor.profile.create');
        }

        $request->validate([
            'bank_name' => 'nullable|string|max:100',
            'bank_account_title' => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_iban' => 'nullable|string|max:50',
            'cnic_front' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'cnic_back' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        $data = $request->only('bank_name', 'bank_account_title', 'bank_account_number', 'bank_iban');

        if ($request->hasFile('cnic_front')) {
            $data['cnic_front_path'] = ImageHelper::uploadAndCompress($request->file('cnic_front'), 'vendor-cnic');
        }
        if ($request->hasFile('cnic_back')) {
            $data['cnic_back_path'] = ImageHelper::uploadAndCompress($request->file('cnic_back'), 'vendor-cnic');
        }

        $profile->update($data);

        return redirect()->route('vendor.onboarding');
    }

    public function skip(Request $request)
    {
        $profile = auth()->user()->vendorProfile;
        if ($profile) {
            $profile->update(['onboarding_completed' => true]);
        }
        return redirect()->route('vendor.dashboard')->with('success', 'Onboarding skipped. You can complete it later.');
    }
}
