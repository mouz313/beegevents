@extends('layouts.app')

@section('title', 'Vendor Profile')

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
.profile-card {
    border: 1px solid var(--border);
    border-radius: 16px;
    overflow: hidden;
    background: white;
    box-shadow: 0 4px 20px rgba(43,38,32,0.06);
}
.profile-card .card-header-custom {
    background: var(--charcoal);
    padding: 24px 28px;
    display: flex;
    align-items: center;
    gap: 16px;
}
.profile-card .card-header-custom h4 {
    color: white;
    font-weight: 700;
    margin: 0;
    font-size: 18px;
}
.profile-card .card-header-custom h4 span { color: var(--gold); }
.profile-card .card-body-custom { padding: 28px; }
.form-label-custom {
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--text-muted);
    margin-bottom: 6px;
}
.input-custom {
    border: 2px solid var(--border);
    border-radius: 10px;
    padding: 10px 14px;
    font-size: 14px;
    transition: border-color 0.2s;
    width: 100%;
    background: white;
}
.input-custom:focus {
    border-color: var(--gold);
    outline: none;
    box-shadow: 0 0 0 3px rgba(212,160,23,0.1);
}
textarea.input-custom { resize: vertical; }
.btn-gold {
    background: var(--gold);
    border: none;
    color: var(--charcoal);
    padding: 10px 24px;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    transition: all 0.2s;
}
.btn-gold:hover {
    background: var(--gold-dark);
    color: white;
}
.btn-outline-gold {
    background: transparent;
    border: 2px solid var(--gold);
    color: var(--gold-dark);
    padding: 9px 23px;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    transition: all 0.2s;
}
.btn-outline-gold:hover {
    background: var(--gold);
    color: white;
}
.logo-preview {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid var(--gold);
}
.logo-placeholder {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    background: var(--cream);
    border: 2px dashed var(--border);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-muted);
    font-size: 24px;
}
.section-title {
    font-size: 13px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--gold-dark);
    margin: 24px 0 12px;
    padding-bottom: 6px;
    border-bottom: 2px solid var(--cream);
}
</style>
@endpush

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="profile-card">
                <div class="card-header-custom">
                    <div style="width:44px;height:44px;border-radius:10px;background:var(--gold);display:flex;align-items:center;justify-content:center;font-size:20px;font-weight:700;color:var(--charcoal);flex-shrink:0;">
                        <i class="ti ti-building-store"></i>
                    </div>
                    <div>
                        <h4>{{ $profile ? 'Update' : 'Create' }} <span>Vendor Profile</span></h4>
                        <div style="font-size:12px;color:rgba(255,255,255,0.5);">Manage your business information</div>
                    </div>
                </div>
                <div class="card-body-custom">
                    @if(session('success'))
                        <div class="alert alert-success" style="border-radius:10px;font-size:13px;">{{ session('success') }}</div>
                    @endif

                    <form id="vendorProfileForm" method="POST" enctype="multipart/form-data"
                          action="{{ $profile ? route('vendor.profile.update', $profile) : route('vendor.profile.store') }}">
                        @csrf
                        @if($profile) @method('PUT') @endif

                        <div class="text-center mb-4">
                            @if($profile && $profile->logo_path)
                                <img src="{{ asset('storage/' . $profile->logo_path) }}" class="logo-preview" id="logoPreview">
                            @else
                                <div class="logo-placeholder mx-auto" id="logoPlaceholder">
                                    <i class="ti ti-camera"></i>
                                </div>
                            @endif
                            <div class="mt-2">
                                <label for="logo" class="btn btn-outline-gold btn-sm" style="cursor:pointer;font-size:12px;">
                                    <i class="ti ti-upload"></i> Upload Logo
                                </label>
                                <input type="file" id="logo" name="logo" accept="image/*" style="display:none;" onchange="document.getElementById('logoPreview').src=URL.createObjectURL(this.files[0]);document.getElementById('logoPreview').style.display='block';document.getElementById('logoPlaceholder')&&(document.getElementById('logoPlaceholder').style.display='none');">
                            </div>
                        </div>

                        <div class="section-title">Business Information</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label-custom">Business Name</label>
                                <input type="text" class="input-custom @error('business_name') is-invalid @enderror"
                                       name="business_name" value="{{ old('business_name', $profile->business_name ?? '') }}" required>
                                @error('business_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-custom">Vendor Type</label>
                                <select class="input-custom @error('vendor_type') is-invalid @enderror" name="vendor_type" required>
                                    <option value="">Select Type</option>
                                    <option value="hall" {{ old('vendor_type', $profile->vendor_type ?? '') == 'hall' ? 'selected' : '' }}>Hall</option>
                                    <option value="farmhouse" {{ old('vendor_type', $profile->vendor_type ?? '') == 'farmhouse' ? 'selected' : '' }}>Farmhouse</option>
                                    <option value="decor" {{ old('vendor_type', $profile->vendor_type ?? '') == 'decor' ? 'selected' : '' }}>Decor</option>
                                    <option value="catering" {{ old('vendor_type', $profile->vendor_type ?? '') == 'catering' ? 'selected' : '' }}>Catering</option>
                                    <option value="photography" {{ old('vendor_type', $profile->vendor_type ?? '') == 'photography' ? 'selected' : '' }}>Photography</option>
                                    <option value="dj" {{ old('vendor_type', $profile->vendor_type ?? '') == 'dj' ? 'selected' : '' }}>DJ / Sound</option>
                                    <option value="car" {{ old('vendor_type', $profile->vendor_type ?? '') == 'car' ? 'selected' : '' }}>Car Rental</option>
                                    <option value="other" {{ old('vendor_type', $profile->vendor_type ?? '') == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('vendor_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-custom">Phone</label>
                                <input type="text" class="input-custom @error('phone') is-invalid @enderror"
                                       name="phone" value="{{ old('phone', $profile->phone ?? '') }}" placeholder="03XX-XXXXXXX">
                                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-custom">City</label>
                                <input type="text" class="input-custom @error('city') is-invalid @enderror"
                                       name="city" value="{{ old('city', $profile->city ?? 'Lahore') }}" required>
                                @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label-custom">Address</label>
                                <input type="text" class="input-custom @error('address') is-invalid @enderror"
                                       name="address" value="{{ old('address', $profile->address ?? '') }}" placeholder="Street address, area">
                                @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="section-title">Cancellation Policy</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label-custom">Free Cancellation (days before event)</label>
                                <input type="number" class="input-custom @error('cancel_free_days') is-invalid @enderror"
                                       name="cancel_free_days" value="{{ old('cancel_free_days', $profile->cancel_free_days ?? '') }}" min="0" placeholder="e.g. 7">
                                @error('cancel_free_days')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-custom">Refund % (after free period)</label>
                                <input type="number" class="input-custom @error('cancel_refund_percent') is-invalid @enderror"
                                       name="cancel_refund_percent" value="{{ old('cancel_refund_percent', $profile->cancel_refund_percent ?? '') }}" min="0" max="100" step="0.01" placeholder="e.g. 50">
                                @error('cancel_refund_percent')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label-custom">Cancellation Policy (display text)</label>
                                <textarea class="input-custom @error('cancellation_policy') is-invalid @enderror"
                                          name="cancellation_policy" rows="3" placeholder="Describe your cancellation policy in words...">{{ old('cancellation_policy', $profile->cancellation_policy ?? '') }}</textarea>
                                @error('cancellation_policy')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="section-title">Bank / Payout Details</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label-custom">Bank Name</label>
                                <input type="text" class="input-custom @error('bank_name') is-invalid @enderror"
                                       name="bank_name" value="{{ old('bank_name', $profile->bank_name ?? '') }}" placeholder="e.g. HBL, Meezan">
                                @error('bank_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-custom">Account Title</label>
                                <input type="text" class="input-custom @error('bank_account_title') is-invalid @enderror"
                                       name="bank_account_title" value="{{ old('bank_account_title', $profile->bank_account_title ?? '') }}">
                                @error('bank_account_title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-custom">Account Number</label>
                                <input type="text" class="input-custom @error('bank_account_number') is-invalid @enderror"
                                       name="bank_account_number" value="{{ old('bank_account_number', $profile->bank_account_number ?? '') }}">
                                @error('bank_account_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-custom">IBAN</label>
                                <input type="text" class="input-custom @error('bank_iban') is-invalid @enderror"
                                       name="bank_iban" value="{{ old('bank_iban', $profile->bank_iban ?? '') }}" placeholder="PK36...">
                                @error('bank_iban')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="section-title">Identity Verification (CNIC)</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label-custom">CNIC Front</label>
                                @if($profile && $profile->cnic_front_path)
                                    <div style="margin-bottom:6px;">
                                        <a href="{{ asset('storage/' . $profile->cnic_front_path) }}" target="_blank" style="font-size:12px;color:var(--gold-dark);">View uploaded file</a>
                                    </div>
                                @endif
                                <input type="file" class="input-custom @error('cnic_front') is-invalid @enderror" name="cnic_front" accept="image/jpeg,image/png">
                                @error('cnic_front')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-custom">CNIC Back</label>
                                @if($profile && $profile->cnic_back_path)
                                    <div style="margin-bottom:6px;">
                                        <a href="{{ asset('storage/' . $profile->cnic_back_path) }}" target="_blank" style="font-size:12px;color:var(--gold-dark);">View uploaded file</a>
                                    </div>
                                @endif
                                <input type="file" class="input-custom @error('cnic_back') is-invalid @enderror" name="cnic_back" accept="image/jpeg,image/png">
                                @error('cnic_back')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="d-flex gap-2 mt-4 pt-3" style="border-top:1px solid var(--border);">
                            <button type="submit" class="btn-gold">
                                <i class="ti ti-device-floppy"></i> {{ $profile ? 'Update Profile' : 'Create Profile' }}
                            </button>
                            <a href="{{ route('vendor.dashboard') }}" class="btn-outline-gold" style="text-decoration:none;display:inline-flex;align-items:center;gap:6px;">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('vendorProfileForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const form = this;
    const formData = new FormData(form);
    const url = form.action;
    const method = form.querySelector('input[name="_method"]')?.value || 'POST';

    fetch(url, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        },
        body: formData,
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            window.location.href = '{{ route("vendor.dashboard") }}';
        } else if (data.errors) {
            Object.keys(data.errors).forEach(key => {
                const input = document.querySelector(`[name="${key}"]`);
                if (input) {
                    input.classList.add('is-invalid');
                    const feedback = input.nextElementSibling || input.parentElement.nextElementSibling;
                    if (feedback && feedback.classList.contains('invalid-feedback')) {
                        feedback.textContent = data.errors[key][0];
                    }
                }
            });
        }
    })
    .catch(err => console.error(err));
});
</script>
@endpush
