@extends('vendor.layouts.master')

@section('title', 'Buy Package')

@section('content')
<div class="vd-page-head">
    <div>
        <h2 class="vd-page-title"><i class="ti ti-box"></i> Buy Package</h2>
        <a href="{{ route('vendor.packages.index') }}" class="vd-back-link">← Back to packages</a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-5">
        <div class="admin-card">
            <div class="card-header">
                <h5><i class="ti ti-box"></i> {{ $package->title }}</h5>
            </div>
            <div class="card-body" style="font-size:14px;">
                <h4 style="color:var(--gold);font-weight:800;">PKR {{ number_format($package->total_price) }}</h4>
                <div class="text-muted" style="font-size:12px;">per {{ $package->duration_days }} days</div>
                <hr style="border-color:var(--border);margin:14px 0;">
                <ul style="font-size:13px;color:var(--text-muted);padding-left:18px;line-height:2;">
                    <li>
                        @if($package->boost_tier)
                            <span style="text-transform:capitalize;"><i class="ti ti-{{ $package->boost_tier === 'premium' ? 'crown' : 'star' }}"></i> {{ $package->boost_tier }} boost</span>
                        @else
                            No boost
                        @endif
                    </li>
                    <li>{{ $package->max_halls !== null ? $package->max_halls.' halls' : 'Unlimited halls' }}</li>
                    <li>{{ $package->max_listings !== null ? $package->max_listings.' services' : 'Unlimited services' }}</li>
                    <li>{{ $package->packageItems->count() }} combo template item(s)</li>
                </ul>
                @if($package->description)
                    <hr style="border-color:var(--border);margin:14px 0;">
                    <p style="font-size:12px;color:var(--text-muted);">{{ $package->description }}</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="admin-card">
            <div class="card-header">
                <h5><i class="ti ti-wallet"></i> Payment</h5>
            </div>
            <div class="card-body">
                <ul class="nav nav-pills mb-3" role="tablist" style="gap:8px;">
                    <li class="nav-item" role="presentation">
                        <button class="btn bm-btn-gold btn-sm" type="button" data-bs-toggle="tab" data-bs-target="#payCard" role="tab" {{ $stripeKey ? '' : 'disabled' }}><i class="ti ti-credit-card"></i> Card</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="btn bm-btn-outline btn-sm" type="button" data-bs-toggle="tab" data-bs-target="#payBank" role="tab"><i class="ti ti-building-bank"></i> Bank Transfer</button>
                    </li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane fade {{ $stripeKey ? 'show active' : '' }}" id="payCard" role="tabpanel">
                        @if($stripeKey)
                            <p style="font-size:13px;color:var(--text-muted);">Pay securely with your card. Your package activates immediately after payment.</p>
                            <button id="payCardBtn" onclick="processPackagePayment()" class="btn bm-btn-gold w-100" style="padding:12px;">
                                <i class="ti ti-credit-card"></i> Pay PKR {{ number_format($package->total_price) }}
                            </button>
                            <div id="cardMsg" style="font-size:13px;margin-top:12px;"></div>
                        @else
                            <p style="font-size:13px;color:var(--text-muted);">Card payments are currently unavailable. Please use bank transfer.</p>
                        @endif
                    </div>
                    <div class="tab-pane fade {{ $stripeKey ? '' : 'show active' }}" id="payBank" role="tabpanel">
                        <p style="font-size:13px;color:var(--text-muted);">Transfer the amount to the account below, then submit the receipt. Admin will activate your package after verification.</p>
                        <div style="background:var(--cream);border-radius:10px;padding:14px;font-size:13px;margin-bottom:14px;">
                            <strong>Bank:</strong> HBL Pakistan<br>
                            <strong>Account:</strong> 1234-5678-9012-3456<br>
                            <strong>Title:</strong> BeeG Events Pvt Ltd<br>
                            <strong>IBAN:</strong> PK36HABB0012345678901
                        </div>
                        <form method="POST" action="{{ route('vendor.packages.manual', $package) }}" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label" style="font-size:12px;font-weight:600;">Transfer Receipt (optional)</label>
                                <input type="file" name="proof" accept=".jpg,.jpeg,.png,.pdf" class="form-control" style="font-size:13px;">
                                <small style="color:var(--text-muted);">JPG, PNG or PDF — max 5MB</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" style="font-size:12px;font-weight:600;">Transaction Notes (optional)</label>
                                <input type="text" name="notes" class="form-control" style="font-size:13px;" placeholder="e.g. transferred from HBL account">
                            </div>
                            <button type="submit" class="btn bm-btn-outline w-100" style="padding:12px;">
                                <i class="ti ti-send"></i> Submit Bank Transfer
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@if($stripeKey)
    @push('scripts')
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        const stripe = Stripe('{{ $stripeKey }}');
        let processing = false;

        async function processPackagePayment() {
            if (processing) return;
            processing = true;

            const btn = document.getElementById('payCardBtn');
            const msg = document.getElementById('cardMsg');
            const origText = btn.innerHTML;
            btn.innerHTML = '<i class="ti ti-loader"></i> Processing...';
            btn.disabled = true;
            msg.textContent = '';

            try {
                const intentRes = await fetch('{{ route("vendor.packages.intent", $package) }}', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'}
                });
                const intentData = await intentRes.json();
                if (!intentData.success) {
                    msg.textContent = intentData.message || 'Could not start payment.';
                    return;
                }

                const {error, paymentIntent} = await stripe.confirmCardPayment(intentData.client_secret);
                if (error) {
                    msg.textContent = error.message;
                    return;
                }

                const confirmRes = await fetch('{{ route("vendor.packages.confirm", $package) }}', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                    body: JSON.stringify({payment_intent_id: paymentIntent.id})
                });
                const confirmData = await confirmRes.json();
                if (confirmData.success) {
                    msg.style.color = 'var(--green)';
                    msg.textContent = 'Payment successful! Your package is now active.';
                    setTimeout(() => { window.location.href = '{{ route("vendor.packages.index") }}'; }, 1200);
                } else {
                    msg.textContent = confirmData.message || 'Payment confirmation failed.';
                }
            } catch (e) {
                msg.textContent = 'Payment error: ' + e.message;
            } finally {
                btn.innerHTML = origText;
                btn.disabled = false;
                processing = false;
            }
        }
    </script>
    @endpush
@endif
