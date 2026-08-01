@extends('vendor.layouts.master')

@section('title', 'Vendor Profile')

@push('styles')
<style>
.account-strip {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 6px 14px;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 12px 18px;
    margin-bottom: 20px;
    font-size: 13px;
}
.account-strip .strip-label {
    font-weight: 700;
    color: var(--gold-dark);
    text-transform: uppercase;
    font-size: 11px;
    letter-spacing: 0.6px;
}
.account-strip .strip-item {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: var(--text-primary);
}
.account-strip .strip-item i { color: var(--gold-dark); font-size: 16px; }
.completion-wrap {
    background: var(--cream);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 14px 18px;
    margin-bottom: 22px;
}
.completion-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 13px;
    font-weight: 700;
    color: var(--charcoal);
    margin-bottom: 8px;
}
.completion-top #completionPct { color: var(--gold-dark); }
.completion-bar {
    height: 10px;
    background: var(--border);
    border-radius: 99px;
    overflow: hidden;
}
.completion-bar-fill {
    height: 100%;
    width: 0%;
    background: linear-gradient(90deg, var(--gold), var(--gold-dark));
    border-radius: 99px;
    transition: width 0.35s ease;
}
.step-indicator {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 0;
    margin-bottom: 28px;
}
.step-indicator .step {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 14px;
    font-size: 13px;
    font-weight: 600;
    color: var(--text-muted);
    cursor: pointer;
    border-radius: 10px;
    transition: color 0.15s;
}
.step-indicator .step:hover { color: var(--gold-dark); }
.step-indicator .step.active { color: var(--gold-dark); }
.step-indicator .step.completed { color: var(--green); }
.step-indicator .step .step-num {
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
    transition: all 0.15s;
}
.step-indicator .step.active .step-num {
    background: var(--gold);
    color: var(--charcoal);
}
.step-indicator .step.completed .step-num {
    background: var(--green);
    color: white;
}
.step-indicator .step:not(:last-child)::after {
    content: '';
    width: 22px;
    height: 2px;
    background: var(--border);
    margin-left: 8px;
    flex-shrink: 0;
}
.step-indicator .step.completed:not(:last-child)::after { background: var(--green); }
.form-step { animation: stepFade 0.25s ease; }
@keyframes stepFade {
    from { opacity: 0; transform: translateY(6px); }
    to { opacity: 1; transform: translateY(0); }
}
.review-summary { display: flex; flex-direction: column; gap: 10px; }
.review-row {
    display: flex;
    align-items: center;
    gap: 12px;
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 12px 16px;
    background: #fff;
}
.review-row .review-step-num {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 13px;
    background: var(--border);
    color: var(--text-muted);
    flex-shrink: 0;
}
.review-row .review-info { flex: 1; }
.review-row .review-title { font-size: 13px; font-weight: 700; color: var(--charcoal); }
.review-row .review-meta { font-size: 12px; color: var(--text-muted); }
.review-row i { font-size: 20px; }
.review-row.ok .review-step-num { background: var(--green); color: white; }
.review-row.ok i.review-ok { color: var(--green); }
.review-row.miss i.review-miss { color: var(--amber); }
.review-note {
    margin-top: 14px;
    font-size: 13px;
    color: var(--text-muted);
    background: var(--cream);
    border: 1px dashed var(--border);
    border-radius: 10px;
    padding: 10px 14px;
}
</style>
@endpush

@section('content')
<div>
    <div class="account-strip">
        <span class="strip-label">Signed in as</span>
        <span class="strip-item"><i class="ti ti-mail"></i> {{ auth()->user()->email }}</span>
        <span style="color:var(--border);">|</span>
        <span class="strip-item"><i class="ti ti-phone"></i> {{ auth()->user()->phone ?: 'Not provided' }}</span>
        <span style="margin-left:auto;font-size:11px;color:var(--text-muted);">
            <i class="ti ti-info-circle"></i> These are from your registration account.
        </span>
    </div>

    <div class="profile-card">
        <div class="card-header-custom">
            <div style="width:44px;height:44px;border-radius:10px;background:var(--gold);display:flex;align-items:center;justify-content:center;font-size:20px;font-weight:700;color:var(--charcoal);flex-shrink:0;">
                <i class="ti ti-building-store"></i>
            </div>
            <div>
                <h4>{{ $profile ? 'Update' : 'Create' }} <span>Vendor Profile</span></h4>
                <div style="font-size:12px;color:rgba(255,255,255,0.5);">Complete your business information step by step</div>
            </div>
        </div>
        <div class="card-body-custom">
            @if(session('success'))
                <div class="alert alert-success" style="border-radius:10px;font-size:13px;">{{ session('success') }}</div>
            @endif

            <div class="completion-wrap">
                <div class="completion-top">
                    <span><i class="ti ti-chart-pie" style="color:var(--gold);"></i> Profile Completion</span>
                    <span id="completionPct">0%</span>
                </div>
                <div class="completion-bar">
                    <div class="completion-bar-fill" id="completionBar"></div>
                </div>
            </div>

            <div class="step-indicator" id="profileSteps">
                <div class="step active" data-step="1" onclick="goToStep(1)">
                    <span class="step-num" id="stepNum1">1</span>
                    Business
                </div>
                <div class="step" data-step="2" onclick="goToStep(2)">
                    <span class="step-num" id="stepNum2">2</span>
                    Specifications
                </div>
                <div class="step" data-step="3" onclick="goToStep(3)">
                    <span class="step-num" id="stepNum3">3</span>
                    Bank &amp; ID
                </div>
                <div class="step" data-step="4" onclick="goToStep(4)">
                    <span class="step-num" id="stepNum4">4</span>
                    Review
                </div>
            </div>

            <form id="vendorProfileForm" method="POST" enctype="multipart/form-data"
                  action="{{ $profile ? route('vendor.profile.update', $profile) : route('vendor.profile.store') }}">
                @csrf
                @if($profile) @method('PUT') @endif

                {{-- STEP 1: Business Information --}}
                <div class="form-step" id="formStep1">
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

                    <div class="section-title"><i class="ti ti-building"></i> Business Information</div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label-custom">Business Name <span style="color:var(--red);">*</span></label>
                            <input type="text" class="input-custom @error('business_name') is-invalid @enderror"
                                   name="business_name" value="{{ old('business_name', $profile->business_name ?? '') }}" required>
                            @error('business_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label-custom">Vendor Type <span style="color:var(--red);">*</span></label>
                            @include('vendor.partials.vendor-type-toggle', [
                                'selectedType' => old('vendor_type', $profile->vendor_type ?? 'hall'),
                                'markChecked' => true,
                            ])
                            @error('vendor_type')<div class="invalid-feedback" id="vendorTypeFeedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-custom">Phone <span style="color:var(--red);">*</span></label>
                            <input type="text" class="input-custom @error('phone') is-invalid @enderror"
                                   name="phone" value="{{ old('phone', $profile->phone ?? '') }}" placeholder="03XX-XXXXXXX" required>
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-custom">City <span style="color:var(--red);">*</span></label>
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
                </div>

                {{-- STEP 2: Specifications & Cancellation --}}
                <div class="form-step" id="formStep2" style="display:none;">
                    @php $specValues = $profile ? $profile->specFormValues() : []; @endphp
                    <div class="section-title"><i class="ti ti-settings"></i> Specifications</div>
                    <div style="font-size:12px;color:var(--text-muted);margin-bottom:4px;">
                        Specifications for your selected vendor type.
                    </div>
                    @include('vendor.partials.contact-legal', ['values' => $specValues])
                    @foreach(config('vendor-specs.types', []) as $typeKey => $typeDef)
                        @include('vendor.partials.type-specs', [
                            'typeKey' => $typeKey,
                            'values' => $specValues,
                            'selected' => $profile->vendor_type ?? old('vendor_type') ?? 'hall',
                        ])
                    @endforeach

                    <div class="section-title"><i class="ti ti-adjustments-horizontal"></i> Cancellation Policy</div>
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
                </div>

                {{-- STEP 3: Bank & Identity --}}
                <div class="form-step" id="formStep3" style="display:none;">
                    <div class="section-title"><i class="ti ti-wallet"></i> Bank / Payout Details</div>
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

                    <div class="section-title"><i class="ti ti-id"></i> Identity Verification (CNIC)</div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label-custom">CNIC Front</label>
                            @if($profile && $profile->cnic_front_path)
                                <div style="margin-bottom:6px;">
                                    <a href="{{ asset('storage/' . $profile->cnic_front_path) }}" target="_blank" style="font-size:12px;color:var(--gold-dark);">View uploaded file</a>
                                </div>
                            @endif
                            <input type="file" class="input-custom @error('cnic_front') is-invalid @enderror" name="cnic_front" accept="image/jpeg,image/png"
                                   data-has-file="{{ $profile && $profile->cnic_front_path ? '1' : '0' }}">
                            @error('cnic_front')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-custom">CNIC Back</label>
                            @if($profile && $profile->cnic_back_path)
                                <div style="margin-bottom:6px;">
                                    <a href="{{ asset('storage/' . $profile->cnic_back_path) }}" target="_blank" style="font-size:12px;color:var(--gold-dark);">View uploaded file</a>
                                </div>
                            @endif
                            <input type="file" class="input-custom @error('cnic_back') is-invalid @enderror" name="cnic_back" accept="image/jpeg,image/png"
                                   data-has-file="{{ $profile && $profile->cnic_back_path ? '1' : '0' }}">
                            @error('cnic_back')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                {{-- STEP 4: Review & Save --}}
                <div class="form-step" id="formStep4" style="display:none;">
                    <div class="section-title"><i class="ti ti-checklist"></i> Review Your Profile</div>
                    <div id="reviewSummary"></div>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4 pt-3" style="border-top:1px solid var(--border);">
                    <button type="button" class="btn-outline-gold" id="prevBtn" onclick="prevStep()" style="visibility:hidden;">
                        <i class="ti ti-arrow-left"></i> Back
                    </button>
                    <div class="d-flex gap-2">
                        <a href="{{ route('vendor.dashboard') }}" class="btn-outline-gold" style="padding:9px 20px;">
                            Cancel
                        </a>
                        <button type="button" class="btn-gold" id="nextBtn" onclick="nextStep()">
                            Next <i class="ti ti-arrow-right"></i>
                        </button>
                        <button type="submit" class="btn-gold" id="saveBtn" style="display:none;">
                            <i class="ti ti-device-floppy"></i> {{ $profile ? 'Update Profile' : 'Create Profile' }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const TOTAL_STEPS = 4;
let currentStep = 1;

const MILESTONES = {
    1: ['business_name', 'vendor_type', 'phone', 'city', 'address'],
    2: ['cancellation_policy', 'contact_person_name', 'contact_person_phone'],
    3: ['bank_name', 'bank_account_title', 'cnic_front', 'cnic_back'],
};

function fieldEl(name) {
    return document.querySelector('#vendorProfileForm [name="' + name + '"]');
}

function currentVendorType() {
    const checked = document.querySelector('#vendorProfileForm input[name="vendor_type"]:checked');
    return checked ? checked.value : '';
}

function isFilled(name) {
    const el = fieldEl(name);
    if (!el) return false;
    if (el.type === 'file') return (el.files && el.files.length > 0) || el.dataset.hasFile === '1';
    if (el.type === 'checkbox') return el.checked;
    if (el.type === 'radio') return currentVendorType() !== '';
    return el.value !== undefined && String(el.value).trim() !== '';
}

function stepFilled(n) {
    return MILESTONES[n].every(isFilled);
}

function updateCompletion() {
    let filled = 0, total = 0;
    for (const n in MILESTONES) {
        filled += MILESTONES[n].filter(isFilled).length;
        total += MILESTONES[n].length;
    }
    const pct = total ? Math.round((filled / total) * 100) : 0;
    document.getElementById('completionPct').textContent = pct + '%';
    document.getElementById('completionBar').style.width = pct + '%';
    renderTimeline();
    renderReview();
}

function renderTimeline() {
    for (let i = 1; i <= TOTAL_STEPS; i++) {
        const step = document.querySelector('#profileSteps .step[data-step="' + i + '"]');
        const num = document.getElementById('stepNum' + i);
        if (!step) continue;
        step.classList.remove('active', 'completed');
        if (i === TOTAL_STEPS) {
            if (currentStep === TOTAL_STEPS) step.classList.add('active');
        } else if (stepFilled(i)) {
            step.classList.add('completed');
            num.textContent = '✓';
        } else if (i === currentStep) {
            step.classList.add('active');
            num.textContent = i;
        } else {
            num.textContent = i;
        }
    }
    document.getElementById('prevBtn').style.visibility = currentStep === 1 ? 'hidden' : 'visible';
    document.getElementById('nextBtn').style.display = currentStep === TOTAL_STEPS ? 'none' : '';
    document.getElementById('saveBtn').style.display = currentStep === TOTAL_STEPS ? '' : 'none';
}

function renderReview() {
    const pct = document.getElementById('completionPct').textContent;
    const rows = [
        { n: 1, t: 'Business Information', fields: MILESTONES[1] },
        { n: 2, t: 'Specifications & Policy', fields: MILESTONES[2] },
        { n: 3, t: 'Bank & Identity', fields: MILESTONES[3] },
    ];
    let html = '<div class="review-summary">';
    rows.forEach(function (r) {
        const filled = r.fields.filter(isFilled).length;
        const ok = filled === r.fields.length;
        html += '<div class="review-row ' + (ok ? 'ok' : 'miss') + '">' +
            '<span class="review-step-num">' + r.n + '</span>' +
            '<div class="review-info"><div class="review-title">' + r.t + '</div>' +
            '<div class="review-meta">' + filled + ' of ' + r.fields.length + ' fields filled</div></div>' +
            '<i class="ti ' + (ok ? 'ti-circle-check-filled review-ok' : 'ti-alert-circle-filled review-miss') + '"></i>' +
            '</div>';
    });
    html += '</div>';
    html += '<div class="review-note"><i class="ti ti-info-circle"></i> Your profile is <b>' + pct + '</b> complete. Review each step, then click <b>Save</b> to keep your changes.</div>';
    document.getElementById('reviewSummary').innerHTML = html;
}

function showStep(n) {
    if (n < 1) n = 1;
    if (n > TOTAL_STEPS) n = TOTAL_STEPS;
    currentStep = n;
    for (let i = 1; i <= TOTAL_STEPS; i++) {
        const el = document.getElementById('formStep' + i);
        if (el) el.style.display = i === n ? '' : 'none';
    }
    renderTimeline();
    renderReview();
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function validateStep(n) {
    const stepEl = document.getElementById('formStep' + n);
    let ok = true;
    stepEl.querySelectorAll('[required]').forEach(function (el) {
        if (!el.checkValidity()) {
            el.reportValidity();
            ok = false;
        }
    });
    return ok;
}

function nextStep() {
    if (!validateStep(currentStep)) return;
    showStep(currentStep + 1);
}

function prevStep() {
    showStep(currentStep - 1);
}

function goToStep(n) {
    showStep(n);
}

function stepOfField(name) {
    for (let i = 1; i <= TOTAL_STEPS; i++) {
        if (document.getElementById('formStep' + i).querySelector('[name="' + name + '"]')) return i;
    }
    return 1;
}

function updateSpecGroups() {
    const type = currentVendorType();
    if (!type) return;
    document.querySelectorAll('.spec-group').forEach(function (group) {
        group.style.display = group.dataset.specGroup === type ? '' : 'none';
    });
}

document.getElementById('vendorProfileForm')?.addEventListener('input', updateCompletion);
document.getElementById('vendorProfileForm')?.addEventListener('change', function (e) {
    if (e.target && e.target.name === 'vendor_type') updateSpecGroups();
    updateCompletion();
});

updateSpecGroups();
showStep(1);
updateCompletion();

document.getElementById('vendorProfileForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const form = this;
    const formData = new FormData(form);
    const url = form.action;
    const method = form.querySelector('input[name="_method"]')?.value || 'POST';
    const saveBtn = document.getElementById('saveBtn');
    saveBtn.disabled = true;
    saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm" style="width:14px;height:14px;"></span> Saving...';

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
            showToast('Profile saved successfully!', 'success');
            setTimeout(function () {
                window.location.href = '{{ route("vendor.dashboard") }}';
            }, 900);
        } else if (data.errors) {
            saveBtn.disabled = false;
            saveBtn.innerHTML = '<i class="ti ti-device-floppy"></i> {{ $profile ? "Update Profile" : "Create Profile" }}';
            const firstKey = Object.keys(data.errors)[0];
            if (firstKey) showStep(stepOfField(firstKey));
            Object.keys(data.errors).forEach(function (key) {
                if (key === 'vendor_type') {
                    const fb = document.getElementById('vendorTypeFeedback');
                    if (fb) {
                        fb.textContent = data.errors[key][0];
                        fb.style.display = 'block';
                    }
                    return;
                }
                const input = fieldEl(key);
                if (input) {
                    input.classList.add('is-invalid');
                    const feedback = input.nextElementSibling || input.parentElement.nextElementSibling;
                    if (feedback && feedback.classList.contains('invalid-feedback')) {
                        feedback.textContent = data.errors[key][0];
                    }
                }
            });
            showToast('Please fix the highlighted fields.', 'error');
        }
    })
    .catch(function (err) {
        saveBtn.disabled = false;
        saveBtn.innerHTML = '<i class="ti ti-device-floppy"></i> {{ $profile ? "Update Profile" : "Create Profile" }}';
        console.error(err);
    });
});
</script>
@endpush
