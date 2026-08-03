@extends('admin.layouts.master')

@section('title', 'Purchase Detail')

@section('content')
<div class="row">
    <div class="col-lg-5">
        <div class="admin-card">
            <div class="card-header">
                <h5><i class="ti ti-receipt-2"></i> Purchase #{{ $vendorPackagePurchase->id }}</h5>
                <div class="d-flex gap-2">
                    @if($vendorPackagePurchase->status === 'pending')
                        <button class="btn btn-gold btn-sm" id="approvePurchaseBtn">Approve</button>
                    @endif
                    <a href="{{ route('admin.package-purchases.index') }}" class="btn btn-ghost btn-sm">Back</a>
                </div>
            </div>
            <div class="card-body">
                <div class="d-flex justify-between align-center mb-2">
                    <span class="text-muted" style="font-size:12px;">Status</span>
                    <span class="status-badge status-{{ $vendorPackagePurchase->status }}">{{ ucfirst($vendorPackagePurchase->status) }}</span>
                </div>
                <div class="d-flex justify-between align-center mb-2">
                    <span class="text-muted" style="font-size:12px;">Amount</span>
                    <strong>PKR {{ number_format($vendorPackagePurchase->amount) }}</strong>
                </div>
                <div class="d-flex justify-between align-center mb-2">
                    <span class="text-muted" style="font-size:12px;">Method</span>
                    <span style="text-transform:capitalize;font-size:13px;">{{ str_replace('_', ' ', $vendorPackagePurchase->method) }}</span>
                </div>
                <div class="d-flex justify-between align-center mb-2">
                    <span class="text-muted" style="font-size:12px;">Duration</span>
                    <span style="font-size:13px;">{{ $vendorPackagePurchase->duration_days ?? '—' }} days</span>
                </div>
                <div class="d-flex justify-between align-center mb-2">
                    <span class="text-muted" style="font-size:12px;">Slots</span>
                    <span style="font-size:13px;">
                        {{ $vendorPackagePurchase->max_halls !== null ? $vendorPackagePurchase->max_halls.' halls' : 'Unlimited halls' }}
                        · {{ $vendorPackagePurchase->max_listings !== null ? $vendorPackagePurchase->max_listings.' services' : 'Unlimited services' }}
                    </span>
                </div>
                <div class="d-flex justify-between align-center mb-2">
                    <span class="text-muted" style="font-size:12px;">Boost</span>
                    <span style="font-size:13px;text-transform:capitalize;">
                        {{ $vendorPackagePurchase->boost_tier ? '<i class="ti ti-crown"></i> '.$vendorPackagePurchase->boost_tier : 'None' }}
                    </span>
                </div>
                <div class="d-flex justify-between align-center mb-2">
                    <span class="text-muted" style="font-size:12px;">Transaction ID</span>
                    <span style="font-size:12px;">{{ $vendorPackagePurchase->transaction_id ?? '—' }}</span>
                </div>
                @if($vendorPackagePurchase->proof_path)
                    <div class="d-flex justify-between align-center mb-2">
                        <span class="text-muted" style="font-size:12px;">Proof</span>
                        <a href="{{ asset('storage/'.$vendorPackagePurchase->proof_path) }}" target="_blank" class="btn btn-ghost btn-sm">View Proof</a>
                    </div>
                @endif
                @if($vendorPackagePurchase->notes)
                    <div class="d-flex justify-between align-center mb-2">
                        <span class="text-muted" style="font-size:12px;">Notes</span>
                        <span style="font-size:12px;">{{ $vendorPackagePurchase->notes }}</span>
                    </div>
                @endif
                @if($vendorPackagePurchase->ends_at)
                    <div class="d-flex justify-between align-center mb-2">
                        <span class="text-muted" style="font-size:12px;">Valid Until</span>
                        <span style="font-size:13px;">{{ $vendorPackagePurchase->ends_at->format('M d, Y') }}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="admin-card mb-4">
            <div class="card-header">
                <h5><i class="ti ti-building-store"></i> Vendor</h5>
                <a href="{{ route('admin.vendors.show', $vendorPackagePurchase->vendorProfile) }}" class="btn btn-ghost btn-sm">View Vendor</a>
            </div>
            <div class="card-body" style="font-size:13px;">
                <strong>{{ $vendorPackagePurchase->vendorProfile->business_name }}</strong>
                <div class="text-muted">{{ $vendorPackagePurchase->vendorProfile->user->name ?? '' }} · {{ $vendorPackagePurchase->vendorProfile->user->email ?? '' }}</div>
                <div class="mt-2">
                    <span class="status-badge status-{{ $vendorPackagePurchase->vendorProfile->status }}">{{ ucfirst($vendorPackagePurchase->vendorProfile->status) }}</span>
                </div>
            </div>
        </div>

        <div class="admin-card">
            <div class="card-header">
                <h5><i class="ti ti-box"></i> {{ $vendorPackagePurchase->package->title ?? 'Package' }}</h5>
            </div>
            <div class="card-body">
                @if($vendorPackagePurchase->package)
                    <p style="font-size:13px;color:var(--text-muted);">{{ $vendorPackagePurchase->package->description ?: 'No description.' }}</p>
                    <div class="text-muted" style="font-size:12px;">
                        {{ $vendorPackagePurchase->package->packageItems->count() }} combo template item(s)
                    </div>
                @else
                    <p class="text-muted" style="font-size:13px;">Package record no longer available.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('approvePurchaseBtn')?.addEventListener('click', function() {
    if (!confirm('Approve this purchase and activate the package for the vendor?')) return;
    const btn = this;
    btn.disabled = true;
    btn.innerHTML = '<i class="ti ti-loader"></i> Approving...';

    fetch('{{ route("admin.package-purchases.approve", $vendorPackagePurchase) }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showToast('success', 'Approved', 'Package activated for the vendor.');
            setTimeout(() => location.reload(), 1200);
        } else {
            showToast('error', 'Error', data.message || 'Could not approve purchase.');
            btn.disabled = false;
            btn.innerHTML = 'Approve';
        }
    })
    .catch(() => {
        showToast('error', 'Error', 'Something went wrong.');
        btn.disabled = false;
        btn.innerHTML = 'Approve';
    });
});
</script>
@endpush
