<?php

namespace App\Http\Controllers\Vendor;

use App\Helpers\ImageHelper;
use App\Http\Controllers\Controller;
use App\Models\VendorProfile;
use App\Services\VendorSpecService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string|max:500',
            'cancellation_policy' => 'nullable|string',
            'cancel_free_days' => 'nullable|integer|min:0',
            'cancel_refund_percent' => 'nullable|numeric|min:0|max:100',
            'bank_name' => 'required|string|max:100',
            'bank_account_title' => 'required|string|max:255',
            'bank_account_number' => 'required|string|max:50',
            'bank_iban' => 'required|string|max:50',
            'cnic_front' => 'required|image|mimes:jpeg,png,jpg|max:5120',
            'cnic_back' => 'required|image|mimes:jpeg,png,jpg|max:5120',
            'legal_doc' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
        ] + VendorSpecService::rulesFor($request->vendor_type ?? 'hall'));

        $data = [
            'user_id' => auth()->id(),
            'trial_ends_at' => now()->addDays(3),
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
        if ($request->hasFile('legal_doc')) {
            $data['legal_doc_path'] = $request->file('legal_doc')->store('vendor-legal', 'public');
        }

        $profile = VendorProfile::create($data);

        VendorSpecService::save($profile, $request);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'profile' => $profile]);
        }

        return redirect()->route('vendor.dashboard')->with('success', 'Profile created!');
    }

    public function update(Request $request, VendorProfile $vendorProfile)
    {
        abort_unless($vendorProfile->user_id === auth()->id(), 403);

        $request->validate([
            'business_name' => 'required|string|max:255',
            'vendor_type' => 'required|in:hall,farmhouse,decor,catering,photography,dj,car,other',
            'city' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string|max:500',
            'cancellation_policy' => 'nullable|string',
            'cancel_free_days' => 'nullable|integer|min:0',
            'cancel_refund_percent' => 'nullable|numeric|min:0|max:100',
            'bank_name' => [Rule::requiredIf(empty($vendorProfile->bank_name)), 'string', 'max:100'],
            'bank_account_title' => [Rule::requiredIf(empty($vendorProfile->bank_account_title)), 'string', 'max:255'],
            'bank_account_number' => [Rule::requiredIf(empty($vendorProfile->bank_account_number)), 'string', 'max:50'],
            'bank_iban' => [Rule::requiredIf(empty($vendorProfile->bank_iban)), 'string', 'max:50'],
            'cnic_front' => [Rule::requiredIf(empty($vendorProfile->cnic_front_path)), 'image', 'mimes:jpeg,png,jpg', 'max:5120'],
            'cnic_back' => [Rule::requiredIf(empty($vendorProfile->cnic_back_path)), 'image', 'mimes:jpeg,png,jpg', 'max:5120'],
            'legal_doc' => [Rule::requiredIf(empty($vendorProfile->legal_doc_path)), 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
        ] + VendorSpecService::rulesFor($request->vendor_type ?? $vendorProfile->vendor_type ?? 'hall'));

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
        if ($request->hasFile('legal_doc')) {
            $data['legal_doc_path'] = $request->file('legal_doc')->store('vendor-legal', 'public');
        }

        $vendorProfile->update($data);

        VendorSpecService::save($vendorProfile, $request);

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
