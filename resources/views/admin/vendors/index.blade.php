@extends('admin.layouts.master')

@section('title', 'All Vendors')

@section('content')
<style>
.kyc-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 10px;
    border-radius: 99px;
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    white-space: nowrap;
}
.kyc-badge-verified { background: rgba(40,167,69,0.12); color: #28a745; }
.kyc-badge-pending { background: rgba(212,160,23,0.15); color: #b8860b; }
.kyc-badge-suspended, .kyc-badge-incomplete { background: rgba(220,53,69,0.12); color: #dc3545; }
</style>
{{-- Stats Row --}}
@php
    $totalVendors = \App\Models\VendorProfile::count();
    $pendingCount = \App\Models\VendorProfile::where('status', 'pending')->count();
    $verifiedCount = \App\Models\VendorProfile::where('status', 'verified')->count();
    $suspendedCount = \App\Models\VendorProfile::where('status', 'suspended')->count();
    $blockedCount = \App\Models\VendorProfile::where('status', 'blocked')->count();
@endphp
<div class="stats-grid mb-4" style="grid-template-columns:repeat(5,1fr);">
    <div class="stat-card" style="border-left:3px solid var(--blue-grey);">
        <div class="stat-label">Total Vendors</div>
        <div class="stat-value">{{ $totalVendors }}</div>
    </div>
    <div class="stat-card" style="border-left:3px solid var(--amber);">
        <div class="stat-label">Pending</div>
        <div class="stat-value" style="color:var(--amber);">{{ $pendingCount }}</div>
    </div>
    <div class="stat-card" style="border-left:3px solid var(--green);">
        <div class="stat-label">Verified</div>
        <div class="stat-value" style="color:var(--green);">{{ $verifiedCount }}</div>
    </div>
    <div class="stat-card" style="border-left:3px solid var(--red);">
        <div class="stat-label">Blocked</div>
        <div class="stat-value" style="color:var(--red);">{{ $blockedCount }}</div>
    </div>
    <div class="stat-card" style="border-left:3px solid var(--blue-grey);">
        <div class="stat-label">Suspended</div>
        <div class="stat-value">{{ $suspendedCount }}</div>
    </div>
</div>

<div class="admin-card">
    <div class="card-header">
        <h5><i class="ti ti-building-store"></i> All Vendors</h5>
        <div class="d-flex gap-2 align-items-center">
            @if(request('kyc') === 'incomplete')
                <span class="status-badge" style="background:rgba(220,53,69,0.12);color:#dc3545;">
                    <i class="ti ti-shield-check"></i> KYC Incomplete
                </span>
            @endif
            <span style="font-size:12px;color:var(--text-muted);">{{ $vendors->total() }} total</span>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="p-3 border-bottom" style="background:var(--cream);">
            <form method="GET" action="{{ route('admin.vendors.index') }}" class="row g-2 align-items-end">
                @if(request('kyc') === 'incomplete')
                    <input type="hidden" name="kyc" value="incomplete">
                @endif
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text" style="background:var(--white);border:1px solid var(--border);border-right:none;border-radius:8px 0 0 8px;">
                            <i class="ti ti-search" style="font-size:14px;color:var(--text-muted);"></i>
                        </span>
                        <input type="text" name="search" class="form-control"
                               placeholder="Search by business name, owner, email, phone, or city..."
                               value="{{ request('search') }}"
                               style="border-left:none;border-radius:0 8px 8px 0;font-size:13px;">
                    </div>
                </div>
                <div class="col-md-2 d-flex gap-1">
                    <button type="submit" class="btn btn-gold btn-sm">Search</button>
                    @if(request('search') || request('kyc'))
                        <a href="{{ route('admin.vendors.index') }}" class="btn btn-ghost btn-sm">Clear</a>
                    @endif
                </div>
            </form>
        </div>

        @if(request('kyc') === 'incomplete' && $vendors->count() > 0)
            <div class="px-3 py-2 border-bottom" style="background:#FDECEC;font-size:12px;color:#b02a37;">
                <i class="ti ti-alert-triangle"></i> Showing vendors missing at least one required KYC field. They cannot be verified until KYC is complete.
            </div>
        @endif

        @if($vendors->count() > 0)
            <div style="overflow-x:auto;">
                <table class="table-admin">
                    <thead>
                        <tr>
                            <th style="padding-left:20px;">Business</th>
                            <th>Owner</th>
                            <th>Phone</th>
                            <th>Type</th>
                            <th>City</th>
                            <th>Status</th>
                            <th>KYC</th>
                            <th style="width:130px;padding-right:20px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($vendors as $vendor)
                            <tr>
                                <td style="padding-left:20px;">
                                    <div class="d-flex align-items-center gap-2">
                                        <div style="width:34px;height:34px;border-radius:8px;background:var(--light-honey, #FAEEDA);display:flex;align-items:center;justify-content:center;flex-shrink:0;color:var(--gold-dark);font-weight:700;font-size:14px;">
                                            {{ substr($vendor->business_name, 0, 1) }}
                                        </div>
                                        <div>
                                            <strong style="font-size:13px;">{{ $vendor->business_name }}</strong>
                                            @if($vendor->user->email)
                                                <div style="font-size:11px;color:var(--text-muted);">{{ $vendor->user->email }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span style="font-size:13px;">{{ $vendor->user->name ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    <span style="font-size:12px;color:var(--text-muted);">{{ $vendor->user->phone ?? '—' }}</span>
                                </td>
                                <td>
                                    <span class="status-badge" style="background:rgba(74,101,114,0.1);color:var(--blue-grey);text-transform:capitalize;">
                                        {{ str_replace('_', ' ', $vendor->vendor_type) }}
                                    </span>
                                </td>
                                <td style="font-size:13px;">{{ $vendor->city }}</td>
                                <td>
                                    <span class="status-badge status-{{ $vendor->status }}">
                                        {{ ucfirst($vendor->status) }}
                                    </span>
                                </td>
                                <td>
                                    @php $kycBadge = $vendor->kycBadge(); @endphp
                                    <span class="kyc-badge {{ $kycBadge['class'] }}">{{ $kycBadge['label'] }}</span>
                                </td>
                                <td style="padding-right:20px;">
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('admin.vendors.show', $vendor) }}" class="btn btn-ghost btn-sm" title="View">
                                            <i class="ti ti-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.vendors.edit', $vendor) }}" class="btn btn-ghost btn-sm" title="Edit">
                                            <i class="ti ti-edit"></i>
                                        </a>
                                        <button class="btn btn-ghost btn-sm delete-vendor" data-id="{{ $vendor->id }}" title="Delete">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center px-3 py-3 border-top">
                <span style="font-size:12px;color:var(--text-muted);">
                    Showing {{ $vendors->firstItem() }}–{{ $vendors->lastItem() }} of {{ $vendors->total() }} vendors
                </span>
                <div>
                    {{ $vendors->links() }}
                </div>
            </div>
        @else
            <div class="text-center py-5">
                <i class="ti ti-building-store-off" style="font-size:48px;color:var(--text-muted);opacity:0.4;"></i>
                <h5 style="margin-top:12px;color:var(--text-muted);">
                    @if(request('search'))
                        No results for "{{ request('search') }}"
                    @else
                        No vendors yet
                    @endif
                </h5>
                <p style="font-size:13px;color:var(--text-muted);">
                    @if(request('search'))
                        Try a different search term.
                    @else
                        Vendors will appear here once they register.
                    @endif
                </p>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.delete-vendor').forEach(btn => {
    btn.addEventListener('click', function() {
        if (!confirm('Delete this vendor profile?')) return;
        fetch(this.dataset.id, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: new URLSearchParams({ _method: 'DELETE' })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showToast('success', 'Deleted', 'Vendor profile removed.');
                setTimeout(() => location.reload(), 500);
            }
        });
    });
});
</script>
@endpush
