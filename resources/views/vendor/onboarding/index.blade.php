@extends('vendor.layouts.master')

@section('title', 'Vendor Onboarding')

@push('styles')
<style>
:root {
    --gold: #D4A017;
    --gold-dark: #B8860B;
    --charcoal: #2B2620;
    --cream: #FBF6EC;
    --border: #E8E2D5;
    --text-muted: #6B6660;
}
.step-indicator {
    display: flex;
    justify-content: center;
    gap: 0;
    margin-bottom: 32px;
}
.step {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    font-size: 13px;
    font-weight: 600;
    color: var(--text-muted);
    position: relative;
}
.step.active {
    color: var(--gold-dark);
}
.step.completed {
    color: var(--green);
}
.step .step-num {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: 700;
    background: var(--border);
    color: var(--text-muted);
    flex-shrink: 0;
}
.step.active .step-num {
    background: var(--gold);
    color: var(--charcoal);
}
.step.completed .step-num {
    background: var(--green);
    color: white;
}
.step:not(:last-child)::after {
    content: '';
    width: 24px;
    height: 2px;
    background: var(--border);
    margin-left: 8px;
}
.step.completed:not(:last-child)::after {
    background: var(--green);
}
.kyc-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 12px;
    border-radius: 99px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.4px;
}
.kyc-badge-verified { background: rgba(40,167,69,0.12); color: #28a745; }
.kyc-badge-pending { background: rgba(212,160,23,0.15); color: #b8860b; }
.kyc-badge-suspended, .kyc-badge-incomplete { background: rgba(220,53,69,0.12); color: #dc3545; }
</style>
@endpush

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div style="text-align:center;margin-bottom:24px;">
                <h2 style="color:var(--charcoal);font-weight:800;">Welcome to <span style="color:var(--gold);">BeeG Events</span></h2>
                <p style="color:var(--text-muted);">Complete these steps to start receiving bookings.</p>
                @php $badge = $profile ? $profile->kycBadge() : null; @endphp
                @if($badge)
                    <div class="mt-2">
                        <span class="kyc-badge {{ $badge['class'] }}"><i class="ti ti-shield-check"></i> {{ $badge['label'] }}</span>
                    </div>
                @endif
            </div>

            <div class="step-indicator">
                <div class="step {{ $step > 1 ? 'completed' : 'active' }}">
                    <span class="step-num">{{ $step > 1 ? '✓' : '1' }}</span>
                    Profile
                </div>
                <div class="step {{ $step > 2 ? 'completed' : ($step == 2 ? 'active' : '') }}">
                    <span class="step-num">{{ $step > 2 ? '✓' : '2' }}</span>
                    Identity
                </div>
                <div class="step {{ $step > 3 ? 'completed' : ($step == 3 ? 'active' : '') }}">
                    <span class="step-num">{{ $step > 3 ? '✓' : '3' }}</span>
                    Services
                </div>
                <div class="step {{ $step >= 4 ? 'completed' : ($step == 4 ? 'active' : '') }}">
                    <span class="step-num">{{ $step >= 4 ? '✓' : '4' }}</span>
                    Done
                </div>
            </div>

            @if($step == 1)
                <div class="card" style="border:1px solid var(--border);border-radius:16px;">
                    <div class="card-body p-4">
                        <h5 style="font-weight:700;color:var(--charcoal);">Business Profile</h5>
                        <p style="font-size:13px;color:var(--text-muted);">Tell us about your business.</p>
                        <form method="POST" action="{{ route('vendor.onboarding.step1') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label" style="font-size:12px;font-weight:600;">Business Name</label>
                                    <input type="text" name="business_name" class="form-control" value="{{ old('business_name', $profile->business_name ?? '') }}" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label" style="font-size:12px;font-weight:600;">Vendor Type <span style="color:var(--red);">*</span></label>
                                    @include('vendor.partials.vendor-type-toggle', [
                                        'selectedType' => old('vendor_type', $profile->vendor_type ?? 'hall'),
                                        'markChecked' => true,
                                    ])
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" style="font-size:12px;font-weight:600;">Phone <span style="color:var(--red);">*</span></label>
                                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $profile->phone ?? '') }}" placeholder="03XX-XXXXXXX" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" style="font-size:12px;font-weight:600;">City</label>
                                    <input type="text" name="city" class="form-control" value="{{ old('city', $profile->city ?? 'Lahore') }}" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label" style="font-size:12px;font-weight:600;">Address</label>
                                    <input type="text" name="address" class="form-control" value="{{ old('address', $profile->address ?? '') }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label" style="font-size:12px;font-weight:600;">Cancellation Policy (display text)</label>
                                    <textarea name="cancellation_policy" class="form-control" rows="2">{{ old('cancellation_policy', $profile->cancellation_policy ?? '') }}</textarea>
                                </div>
                            </div>

                            @php $specValues = $profile->specFormValues(); @endphp
                            <div style="font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:var(--gold-dark);margin:20px 0 12px;padding-bottom:6px;border-bottom:2px solid var(--cream);display:flex;align-items:center;gap:8px;">
                                <i class="ti ti-settings"></i> Specifications
                            </div>
                            <div style="font-size:12px;color:var(--text-muted);margin-bottom:4px;">
                                Select your vendor type to show its specification form.
                            </div>
                            @include('vendor.partials.contact-legal', ['values' => $specValues, 'requireKyc' => true])
                            @foreach(config('vendor-specs.types', []) as $typeKey => $typeDef)
                                @include('vendor.partials.type-specs', [
                                    'typeKey' => $typeKey,
                                    'values' => $specValues,
                                    'selected' => $profile->vendor_type ?? old('vendor_type') ?? 'hall',
                                ])
                            @endforeach

                            <button type="submit" class="btn-gold mt-3">Next Step <i class="ti ti-arrow-right"></i></button>
                        </form>
                    </div>
                </div>
            @elseif($step == 2)
                <div class="card" style="border:1px solid var(--border);border-radius:16px;">
                    <div class="card-body p-4">
                        <h5 style="font-weight:700;color:var(--charcoal);">Identity Verification</h5>
                        <p style="font-size:13px;color:var(--text-muted);">Upload your CNIC and bank details for payouts.</p>
                        <form method="POST" action="{{ route('vendor.onboarding.step2') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label" style="font-size:12px;font-weight:600;">CNIC Front <span style="color:var(--red);">*</span></label>
                                    <input type="file" name="cnic_front" class="form-control" accept="image/jpeg,image/png" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" style="font-size:12px;font-weight:600;">CNIC Back <span style="color:var(--red);">*</span></label>
                                    <input type="file" name="cnic_back" class="form-control" accept="image/jpeg,image/png" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" style="font-size:12px;font-weight:600;">Bank Name <span style="color:var(--red);">*</span></label>
                                    <input type="text" name="bank_name" class="form-control" value="{{ old('bank_name', $profile->bank_name ?? '') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" style="font-size:12px;font-weight:600;">Account Title <span style="color:var(--red);">*</span></label>
                                    <input type="text" name="bank_account_title" class="form-control" value="{{ old('bank_account_title', $profile->bank_account_title ?? '') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" style="font-size:12px;font-weight:600;">Account Number <span style="color:var(--red);">*</span></label>
                                    <input type="text" name="bank_account_number" class="form-control" value="{{ old('bank_account_number', $profile->bank_account_number ?? '') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" style="font-size:12px;font-weight:600;">IBAN <span style="color:var(--red);">*</span></label>
                                    <input type="text" name="bank_iban" class="form-control" value="{{ old('bank_iban', $profile->bank_iban ?? '') }}" required>
                                </div>
                            </div>
                            <button type="submit" class="btn-gold mt-3">Next Step <i class="ti ti-arrow-right"></i></button>
                        </form>
                    </div>
                </div>
            @elseif($step == 3)
                <div class="card" style="border:1px solid var(--border);border-radius:16px;">
                    <div class="card-body p-4">
                        <h5 style="font-weight:700;color:var(--charcoal);">Your Services</h5>
                        <p style="font-size:13px;color:var(--text-muted);">Add your first hall or service listing.</p>
                        <div class="d-grid gap-3">
                            @if(in_array($profile->vendor_type ?? '', ['hall', 'farmhouse']))
                                <a href="{{ route('vendor.halls.index') }}" class="btn-gold" style="text-decoration:none;text-align:center;padding:14px;">
                                    <i class="ti ti-building"></i> Add Your First Hall
                                </a>
                            @endif
                            <a href="{{ route('vendor.listings.index') }}" class="btn-outline-gold" style="text-decoration:none;text-align:center;padding:14px;">
                                <i class="ti ti-package"></i> Add a Service Listing
                            </a>
                            <form method="POST" action="{{ route('vendor.onboarding.skip') }}">
                                @csrf
                                <button type="submit" class="btn btn-link" style="color:var(--text-muted);text-decoration:none;font-size:13px;">
                                    Skip for now — I'll add services later
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @elseif($step >= 4)
                <div class="card" style="border:1px solid var(--border);border-radius:16px;text-align:center;padding:40px;">
                    <div style="width:64px;height:64px;background:var(--green);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                        <i class="ti ti-circle-check-filled" style="font-size:32px;color:white;"></i>
                    </div>
                    <h4 style="font-weight:700;color:var(--charcoal);">You're All Set!</h4>
                    <p style="color:var(--text-muted);">Your profile is ready. Start exploring the platform.</p>
                    <div class="d-flex justify-content-center gap-2 mt-3">
                        <a href="{{ route('vendor.dashboard') }}" class="btn-gold" style="text-decoration:none;">
                            <i class="ti ti-dashboard"></i> Go to Dashboard
                        </a>
                        <a href="{{ route('vendor.profile.create') }}" class="btn-outline-gold" style="text-decoration:none;">
                            <i class="ti ti-settings"></i> Full Profile Settings
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function currentVendorType() {
    const checked = document.querySelector('input[name="vendor_type"]:checked');
    return checked ? checked.value : '';
}

function updateSpecGroups() {
    const type = currentVendorType();
    if (!type) return;
    document.querySelectorAll('.spec-group').forEach(function (group) {
        group.style.display = group.dataset.specGroup === type ? '' : 'none';
    });
}

document.querySelectorAll('input[name="vendor_type"]').forEach(function (el) {
    el.addEventListener('change', updateSpecGroups);
});
updateSpecGroups();
</script>
@endpush
