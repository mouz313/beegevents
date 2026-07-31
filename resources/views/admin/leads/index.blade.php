@extends('admin.layouts.master')

@section('title', 'Corporate Leads')

@section('content')
<div class="admin-card">
    <div class="card-header">
        <h5><i class="ti ti-users-group"></i> Corporate Leads</h5>
        <span class="status-badge status-pending">{{ \App\Models\CorporateLead::where('status', 'new')->count() }} new</span>
    </div>
    <div class="card-body p-0">
        @if($leads->count() > 0)
            <table class="table-admin">
                <thead>
                    <tr>
                        <th>Company</th>
                        <th>Contact</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($leads as $lead)
                        <tr>
                            <td><strong>{{ $lead->company_name }}</strong></td>
                            <td>{{ $lead->contact_person }}</td>
                            <td>{{ $lead->phone }}</td>
                            <td><span class="text-muted">{{ $lead->email }}</span></td>
                            <td>
                                <span class="status-badge status-{{ $lead->status == 'converted' ? 'confirmed' : ($lead->status == 'closed' ? 'cancelled' : ($lead->status == 'contacted' ? 'pending' : 'requested')) }}">
                                    {{ ucfirst($lead->status) }}
                                </span>
                            </td>
                            <td>
                                <select class="form-control-admin status-change" data-id="{{ $lead->id }}" style="padding:4px 10px;font-size:12px;">
                                    <option value="new" {{ $lead->status == 'new' ? 'selected' : '' }}>New</option>
                                    <option value="contacted" {{ $lead->status == 'contacted' ? 'selected' : '' }}>Contacted</option>
                                    <option value="converted" {{ $lead->status == 'converted' ? 'selected' : '' }}>Converted</option>
                                    <option value="closed" {{ $lead->status == 'closed' ? 'selected' : '' }}>Closed</option>
                                </select>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="text-center py-5">
                <i class="ti ti-users" style="font-size:36px;color:var(--text-muted);"></i>
                <p class="text-muted mt-2">No leads yet.</p>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.status-change').forEach(sel => {
    sel.addEventListener('change', function() {
        fetch('/admin/leads/' + this.dataset.id + '/status', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ status: this.value })
        })
        .then(res => res.json())
        .then(data => { if (data.success) window.location.reload(); });
    });
});
</script>
@endpush
