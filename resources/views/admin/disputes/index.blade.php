@extends('admin.layouts.master')

@section('title', 'Disputes')

@section('content')
<div class="admin-card">
    <div class="card-header">
        <h5><i class="ti ti-alert-triangle"></i> Disputes</h5>
        <span class="status-badge status-pending">{{ \App\Models\Dispute::where('status', 'open')->count() }} open</span>
    </div>
    <div class="card-body p-0">
        @if($disputes->count() > 0)
            <table class="table-admin">
                <thead>
                    <tr>
                        <th>Booking</th>
                        <th>Raised By</th>
                        <th>Reason</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($disputes as $dispute)
                        <tr id="dispute-{{ $dispute->id }}">
                            <td><strong>#{{ $dispute->booking_id }}</strong></td>
                            <td>{{ $dispute->raisedBy->name ?? 'N/A' }}</td>
                            <td><span class="text-muted" style="font-size:12px;">{{ Str::limit($dispute->reason, 60) }}</span></td>
                            <td>
                                <span class="status-badge status-{{ $dispute->status == 'resolved' ? 'confirmed' : ($dispute->status == 'rejected' ? 'cancelled' : 'pending') }}">
                                    {{ ucfirst($dispute->status) }}
                                </span>
                            </td>
                            <td>
                                @if($dispute->status == 'open')
                                    <button class="btn btn-gold btn-sm resolve-dispute" data-id="{{ $dispute->id }}">Resolve</button>
                                    <button class="btn btn-ghost btn-sm reject-dispute" data-id="{{ $dispute->id }}">Reject</button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="text-center py-5">
                <i class="ti ti-shield-check" style="font-size:36px;color:var(--green);"></i>
                <p class="text-muted mt-2">No disputes — all clear!</p>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.resolve-dispute').forEach(btn => {
    btn.addEventListener('click', function() {
        const notes = prompt('Resolution notes:');
        if (!notes) return;
        fetch('/admin/disputes/' + this.dataset.id + '/resolve', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ resolution_notes: notes })
        })
        .then(res => res.json())
        .then(data => { if (data.success) location.reload(); });
    });
});

document.querySelectorAll('.reject-dispute').forEach(btn => {
    btn.addEventListener('click', function() {
        if (!confirm('Reject this dispute?')) return;
        fetch('/admin/disputes/' + this.dataset.id + '/reject', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => { if (data.success) location.reload(); });
    });
});
</script>
@endpush
