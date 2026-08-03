@extends('admin.layouts.master')

@section('title', 'Vendor Verifications')

@section('content')
<div class="admin-card">
    <div class="card-header">
        <h5><i class="ti ti-building-store"></i> Pending Vendor Verifications</h5>
        <span class="status-badge status-pending">{{ $vendors->count() }} pending</span>
    </div>
    <div class="card-body p-0">
        @if($vendors->count() > 0)
            <table class="table-admin">
                <thead>
                    <tr>
                        <th>Business Name</th>
                        <th>Owner</th>
                        <th>Type</th>
                        <th>City</th>
                        <th>Registered</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($vendors as $vendor)
                        <tr id="vendor-{{ $vendor->id }}">
                            <td><strong>{{ $vendor->business_name }}</strong></td>
                            <td>{{ $vendor->user->name }}<br><span class="text-muted" style="font-size:11px;">{{ $vendor->user->email }}</span></td>
                            <td><span class="status-badge" style="background:rgba(74,101,114,0.12);color:var(--blue-grey);">{{ ucfirst($vendor->vendor_type) }}</span></td>
                            <td>{{ $vendor->city }}</td>
                            <td>{{ $vendor->created_at->format('M d, Y') }}</td>
                            <td>
                                <a href="{{ route('admin.vendors.show', $vendor) }}" class="btn btn-outline-gold btn-sm" title="View Details">
                                    <i class="ti ti-eye"></i> View
                                </a>
                                <button class="btn btn-gold btn-sm verify-vendor" data-id="{{ $vendor->id }}">Approve</button>
                                <button class="btn btn-ghost btn-sm suspend-vendor" data-id="{{ $vendor->id }}">Suspend</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="text-center py-5">
                <i class="ti ti-circle-check" style="font-size:36px;color:var(--green);"></i>
                <p class="text-muted mt-2">All vendors verified!</p>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.verify-vendor').forEach(btn => {
    btn.addEventListener('click', function() {
        if (!confirm('Verify this vendor?')) return;
        fetch(`/admin/vendors/${this.dataset.id}/verify`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const row = document.getElementById('vendor-' + btn.dataset.id);
                if (row) row.style.opacity = '0.3';
                showToast('success', 'Vendor Verified', 'Vendor has been approved successfully.');
                setTimeout(() => { if (row) row.remove(); }, 500);
            } else if (data.message) {
                showToast('error', 'KYC Incomplete', data.message);
            }
        });
    });
});

document.querySelectorAll('.suspend-vendor').forEach(btn => {
    btn.addEventListener('click', function() {
        if (!confirm('Suspend this vendor?')) return;
        fetch(`/admin/vendors/${this.dataset.id}/suspend`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const row = document.getElementById('vendor-' + btn.dataset.id);
                if (row) row.remove();
                showToast('warning', 'Vendor Suspended', 'Vendor has been suspended.');
            }
        });
    });
});
</script>
@endpush
