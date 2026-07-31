@extends('admin.layouts.master')

@section('title', 'Vendor Payouts')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 style="font-size:20px;font-weight:600;">
        <i class="ti ti-wallet"></i> Vendor Payouts
    </h2>
    <span class="status-badge status-pending">{{ \App\Models\Payout::where('status','pending')->count() }} pending</span>
</div>

<div class="admin-card mb-4">
    <div class="card-header">
        <h5><i class="ti ti-building-store"></i> Payouts Due</h5>
    </div>
    <div class="card-body p-0">
        @php $hasDue = $vendors->contains(fn($v) => $v->payout_due > 0); @endphp
        @if($hasDue)
            <table class="table-admin">
                <thead>
                    <tr>
                        <th>Vendor</th>
                        <th>Bank Details</th>
                        <th>Due</th>
                        <th>Record Payout</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($vendors as $vendor)
                        @if($vendor->payout_due > 0)
                            <tr>
                                <td>
                                    <strong>{{ $vendor->business_name }}</strong>
                                    <div class="text-muted" style="font-size:11px;">{{ $vendor->user->name ?? '' }}</div>
                                </td>
                                <td style="font-size:12px;">
                                    {{ $vendor->bank_name ?? 'N/A' }}<br>
                                    <span class="text-muted">{{ $vendor->bank_account_title ?? '' }} · {{ $vendor->bank_account_number ?? '' }}</span>
                                </td>
                                <td><strong>PKR {{ number_format($vendor->payout_due) }}</strong></td>
                                <td>
                                    <form method="POST" action="{{ route('admin.payouts.store') }}" class="d-flex gap-2">
                                        @csrf
                                        <input type="hidden" name="vendor_profile_id" value="{{ $vendor->id }}">
                                        <input type="number" name="amount" step="0.01" min="1" max="{{ $vendor->payout_due }}" class="form-control-admin" style="width:130px;" placeholder="Amount" required>
                                        <button type="submit" class="btn btn-gold btn-sm">Record</button>
                                    </form>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="text-center py-5">
                <i class="ti ti-wallet" style="font-size:36px;color:var(--text-muted);"></i>
                <p class="text-muted mt-2">No payouts due right now.</p>
            </div>
        @endif
    </div>
</div>

<div class="admin-card">
    <div class="card-header">
        <h5><i class="ti ti-history"></i> Payout History</h5>
    </div>
    <div class="card-body p-0">
        @if($payouts->count() > 0)
            <table class="table-admin">
                <thead>
                    <tr>
                        <th>Vendor</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payouts as $payout)
                        <tr>
                            <td>{{ $payout->vendorProfile->business_name ?? 'N/A' }}</td>
                            <td><strong>PKR {{ number_format($payout->amount) }}</strong></td>
                            <td style="font-size:12px;">{{ $payout->created_at->format('M d, Y') }}</td>
                            <td>
                                @if($payout->status === 'pending')
                                    <button class="btn btn-gold btn-sm process-payout" data-id="{{ $payout->id }}" style="font-size:11px;">Mark Processed</button>
                                @else
                                    <span class="status-badge status-{{ $payout->status === 'processed' ? 'confirmed' : 'cancelled' }}">
                                        {{ ucfirst($payout->status) }}
                                    </span>
                                @endif
                            </td>
                            <td style="font-size:11px;color:var(--text-muted);">{{ $payout->notes }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="p-3"> {{ $payouts->links() }} </div>
        @else
            <div class="text-center py-5">
                <i class="ti ti-history" style="font-size:36px;color:var(--text-muted);"></i>
                <p class="text-muted mt-2">No payouts recorded yet.</p>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.process-payout').forEach(btn => {
    btn.addEventListener('click', function() {
        if (!confirm('Mark this payout as processed?')) return;
        fetch('/admin/payouts/' + this.dataset.id + '/processed', {
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
                showToast('success', 'Payout Processed', 'Payout marked as processed.');
                setTimeout(() => location.reload(), 1200);
            }
        });
    });
});
</script>
@endpush
