@extends('admin.layouts.master')

@section('title', 'Dispute Detail')

@section('content')
<div class="row">
    <div class="col-lg-5">
        <div class="admin-card">
            <div class="card-header">
                <h5><i class="ti ti-alert-triangle"></i> Dispute #{{ $dispute->id }}</h5>
                <a href="{{ route('admin.disputes.index') }}" class="btn btn-ghost btn-sm">Back</a>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="text-muted" style="font-size:12px;">Status</div>
                    <span class="status-badge status-{{ $dispute->status == 'resolved' ? 'confirmed' : ($dispute->status == 'rejected' ? 'cancelled' : 'pending') }}">
                        {{ ucfirst($dispute->status) }}
                    </span>
                </div>
                <div class="mb-3">
                    <div class="text-muted" style="font-size:12px;">Raised By</div>
                    <strong>{{ $dispute->raisedBy->name ?? 'N/A' }}</strong>
                    <div class="text-muted" style="font-size:11px;">{{ $dispute->raisedBy->email ?? '' }}</div>
                </div>
                <div class="mb-3">
                    <div class="text-muted" style="font-size:12px;">Booking</div>
                    <a href="{{ route('admin.bookings.show', $dispute->booking) }}"><strong>#{{ $dispute->booking_id }}</strong></a>
                </div>
                <div class="mb-3">
                    <div class="text-muted" style="font-size:12px;">Submitted</div>
                    <div>{{ $dispute->created_at->format('M d, Y \a\t h:i A') }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="admin-card">
            <div class="card-header">
                <h5><i class="ti ti-message"></i> Reason</h5>
            </div>
            <div class="card-body">
                <p>{{ $dispute->reason }}</p>
            </div>
        </div>

        @if($dispute->resolution_notes)
            <div class="admin-card">
                <div class="card-header">
                    <h5><i class="ti ti-file-description"></i> Resolution Notes</h5>
                </div>
                <div class="card-body">
                    <p>{{ $dispute->resolution_notes }}</p>
                </div>
            </div>
        @endif

        @if($dispute->status == 'open')
            <div class="admin-card">
                <div class="card-header">
                    <h5><i class="ti ti-judge"></i> Take Action</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label" style="font-size:12px;font-weight:600;">Resolution Notes</label>
                        <textarea class="form-control-admin w-100" id="resolutionNotes" rows="3" placeholder="Enter resolution notes..."></textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-gold" id="resolveBtn">Resolve</button>
                        <button class="btn btn-ghost" id="rejectBtn">Reject</button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('resolveBtn')?.addEventListener('click', function() {
    const notes = document.getElementById('resolutionNotes').value;
    if (!notes) { showToast('error', 'Required', 'Please enter resolution notes.'); return; }
    fetch('{{ route("admin.disputes.resolve", $dispute) }}', {
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

document.getElementById('rejectBtn')?.addEventListener('click', function() {
    if (!confirm('Reject this dispute?')) return;
    const notes = document.getElementById('resolutionNotes').value;
    fetch('{{ route("admin.disputes.reject", $dispute) }}', {
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
</script>
@endpush
