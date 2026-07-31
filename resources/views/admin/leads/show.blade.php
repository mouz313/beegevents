@extends('admin.layouts.master')

@section('title', 'Lead Detail')

@section('content')
<div class="row">
    <div class="col-lg-5">
        <div class="admin-card">
            <div class="card-header">
                <h5><i class="ti ti-users-group"></i> Lead Details</h5>
                <a href="{{ route('admin.leads.index') }}" class="btn btn-ghost btn-sm">Back</a>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="text-muted" style="font-size:12px;">Company Name</div>
                    <strong>{{ $corporateLead->company_name }}</strong>
                </div>
                <div class="mb-3">
                    <div class="text-muted" style="font-size:12px;">Contact Person</div>
                    <strong>{{ $corporateLead->contact_person }}</strong>
                </div>
                <div class="mb-3">
                    <div class="text-muted" style="font-size:12px;">Email</div>
                    <a href="mailto:{{ $corporateLead->email }}">{{ $corporateLead->email }}</a>
                </div>
                <div class="mb-3">
                    <div class="text-muted" style="font-size:12px;">Phone</div>
                    <a href="tel:{{ $corporateLead->phone }}">{{ $corporateLead->phone }}</a>
                </div>
                <div class="mb-3">
                    <div class="text-muted" style="font-size:12px;">Status</div>
                    <select class="form-control-admin status-change" data-id="{{ $corporateLead->id }}" style="padding:6px 12px;font-size:13px;">
                        <option value="new" {{ $corporateLead->status == 'new' ? 'selected' : '' }}>New</option>
                        <option value="contacted" {{ $corporateLead->status == 'contacted' ? 'selected' : '' }}>Contacted</option>
                        <option value="converted" {{ $corporateLead->status == 'converted' ? 'selected' : '' }}>Converted</option>
                        <option value="closed" {{ $corporateLead->status == 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                </div>
                <div class="mb-3">
                    <div class="text-muted" style="font-size:12px;">Submitted</div>
                    <div>{{ $corporateLead->created_at->format('M d, Y \a\t h:i A') }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="admin-card">
            <div class="card-header">
                <h5><i class="ti ti-file-description"></i> Requirements</h5>
            </div>
            <div class="card-body">
                <p>{{ $corporateLead->requirement_notes ?? 'No requirements specified.' }}</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelector('.status-change')?.addEventListener('change', function() {
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
    .then(data => {
        if (data.success) showToast('success', 'Updated', 'Lead status updated.');
    });
});
</script>
@endpush
