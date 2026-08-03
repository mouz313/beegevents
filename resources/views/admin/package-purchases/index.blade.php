@extends('admin.layouts.master')

@section('title', 'Package Purchases')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 style="font-size:20px;font-weight:600;">
        <i class="ti ti-receipt-2"></i> Package Purchases
    </h2>
    <span class="status-badge status-pending">{{ \App\Models\VendorPackagePurchase::where('status','pending')->count() }} pending</span>
</div>

<div class="admin-card">
    <div class="card-header">
        <h5><i class="ti ti-receipt-2"></i> All Purchases</h5>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.package-purchases.index') }}" class="btn btn-ghost btn-sm {{ !request('status') ? 'active' : '' }}">All</a>
            <a href="{{ route('admin.package-purchases.index', ['status' => 'pending']) }}" class="btn btn-ghost btn-sm {{ request('status') === 'pending' ? 'active' : '' }}">Pending</a>
            <a href="{{ route('admin.package-purchases.index', ['status' => 'active']) }}" class="btn btn-ghost btn-sm {{ request('status') === 'active' ? 'active' : '' }}">Active</a>
            <a href="{{ route('admin.package-purchases.index', ['status' => 'expired']) }}" class="btn btn-ghost btn-sm {{ request('status') === 'expired' ? 'active' : '' }}">Expired</a>
        </div>
    </div>
    <div class="card-body p-0">
        @if($purchases->count() > 0)
            <table class="table-admin">
                <thead>
                    <tr>
                        <th>Vendor</th>
                        <th>Package</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Valid</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purchases as $purchase)
                        <tr>
                            <td>
                                <strong>{{ $purchase->vendorProfile->business_name ?? 'N/A' }}</strong>
                                <div class="text-muted" style="font-size:11px;">{{ $purchase->vendorProfile->user->name ?? '' }}</div>
                            </td>
                            <td style="font-size:12px;">
                                <strong>{{ $purchase->package->title ?? 'Package #'.$purchase->package_id }}</strong>
                                @if($purchase->boost_tier)
                                    <br><span class="text-muted" style="text-transform:capitalize;"><i class="ti ti-{{ $purchase->boost_tier === 'premium' ? 'crown' : 'star' }}"></i> {{ $purchase->boost_tier }} boost</span>
                                @endif
                            </td>
                            <td><strong>PKR {{ number_format($purchase->amount) }}</strong></td>
                            <td style="font-size:12px;text-transform:capitalize;">{{ str_replace('_', ' ', $purchase->method) }}</td>
                            <td style="font-size:12px;">
                                @if($purchase->ends_at)
                                    {{ $purchase->ends_at->format('M d, Y') }}
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="status-badge status-{{ $purchase->status }}">{{ ucfirst($purchase->status) }}</span>
                            </td>
                            <td>
                                <a href="{{ route('admin.package-purchases.show', $purchase) }}" class="btn btn-ghost btn-sm" title="View"><i class="ti ti-eye"></i></a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="p-3"> {{ $purchases->links() }} </div>
        @else
            <div class="text-center py-5">
                <i class="ti ti-receipt-2" style="font-size:36px;color:var(--text-muted);"></i>
                <p class="text-muted mt-2">No purchases yet.</p>
            </div>
        @endif
    </div>
</div>
@endsection
