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
                @if($corporateLead->user)
                    <div style="font-size:12px;color:var(--text-muted);margin-top:8px;">
                        <i class="ti ti-user-check"></i> Linked to customer account: <strong>{{ $corporateLead->user->name }}</strong>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Quotations --}}
<div class="row mt-4">
    <div class="col-lg-7">
        <div class="admin-card">
            <div class="card-header">
                <h5><i class="ti ti-file-invoice"></i> Quotations ({{ $corporateLead->quotations->count() }})</h5>
            </div>
            <div class="card-body p-0">
                @if($corporateLead->quotations->count() > 0)
                    <table class="table-admin">
                        <thead>
                            <tr>
                                <th>Quotation</th>
                                <th>Event Date</th>
                                <th>Venue</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($corporateLead->quotations as $quotation)
                                <tr>
                                    <td><strong>{{ $quotation->quote_no }}</strong></td>
                                    <td>{{ $quotation->event_date?->format('M d, Y') ?? '—' }}</td>
                                    <td>{{ $quotation->venue ?? '—' }}</td>
                                    <td>PKR {{ number_format($quotation->amount ?? 0) }}</td>
                                    <td>
                                        <select class="form-control-admin quote-status-change" data-id="{{ $quotation->id }}" style="padding:4px 10px;font-size:12px;">
                                            <option value="draft" {{ $quotation->status == 'draft' ? 'selected' : '' }}>Draft</option>
                                            <option value="sent" {{ $quotation->status == 'sent' ? 'selected' : '' }}>Sent</option>
                                            <option value="accepted" {{ $quotation->status == 'accepted' ? 'selected' : '' }}>Accepted</option>
                                            <option value="declined" {{ $quotation->status == 'declined' ? 'selected' : '' }}>Declined</option>
                                        </select>
                                    </td>
                                    <td style="white-space:nowrap;">
                                        <a href="{{ route('corporate.quotations.show', $quotation->token) }}" target="_blank" class="btn btn-ghost btn-sm" title="View public link">
                                            <i class="ti ti-external-link"></i>
                                        </a>
                                        <button class="btn btn-ghost btn-sm copy-link" data-link="{{ route('corporate.quotations.show', $quotation->token) }}" title="Copy shareable link">
                                            <i class="ti ti-link"></i>
                                        </button>
                                        <button class="btn btn-ghost btn-sm text-danger delete-quote" data-id="{{ $quotation->id }}" title="Delete">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="text-center py-4">
                        <p class="text-muted mb-0">No quotations yet for this lead.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="admin-card">
            <div class="card-header">
                <h5><i class="ti ti-plus"></i> Create Quotation</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.leads.quotations.store', $corporateLead) }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:12px;font-weight:600;">Event Date</label>
                            <input type="date" name="event_date" class="form-control-admin" value="{{ old('event_date') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:12px;font-weight:600;">Seating Capacity</label>
                            <input type="number" name="seating_capacity" class="form-control-admin" min="1" value="{{ old('seating_capacity') }}" placeholder="e.g. 500">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:12px;font-weight:600;">Venue</label>
                            <input type="text" name="venue" class="form-control-admin" value="{{ old('venue') }}" placeholder="e.g. Pearl Continental Lahore">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:12px;font-weight:600;">Client Budget (PKR)</label>
                            <input type="number" name="budget" class="form-control-admin" min="0" step="0.01" value="{{ old('budget') }}" placeholder="e.g. 2000000">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:12px;font-weight:600;">Quoted Amount (PKR)</label>
                            <input type="number" name="amount" class="form-control-admin" min="0" step="0.01" value="{{ old('amount') }}" placeholder="e.g. 1850000" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:12px;font-weight:600;">Valid Until</label>
                            <input type="date" name="valid_until" class="form-control-admin" min="{{ date('Y-m-d') }}" value="{{ old('valid_until') }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label" style="font-size:12px;font-weight:600;">Inclusions</label>
                            <textarea name="inclusions" rows="4" class="form-control-admin" placeholder="Venue &amp; ballroom, in-house catering, stage, seating, sound system...">{{ old('inclusions') }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label" style="font-size:12px;font-weight:600;">Notes</label>
                            <textarea name="notes" rows="2" class="form-control-admin" placeholder="Anything to clarify for the client.">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-gold w-100 mt-3" style="border:none;padding:10px;">
                        <i class="ti ti-file-invoice"></i> Create Quotation
                    </button>
                </form>
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

document.querySelectorAll('.quote-status-change').forEach(sel => {
    sel.addEventListener('change', function() {
        fetch('/admin/quotations/' + this.dataset.id + '/status', {
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
            if (data.success) {
                showToast('success', 'Updated', 'Quotation status updated.');
                if (this.value === 'accepted') setTimeout(() => window.location.reload(), 800);
            }
        });
    });
});

document.querySelectorAll('.copy-link').forEach(btn => {
    btn.addEventListener('click', function() {
        navigator.clipboard.writeText(this.dataset.link);
        showToast('success', 'Copied', 'Quotation link copied to clipboard.');
    });
});

document.querySelectorAll('.delete-quote').forEach(btn => {
    btn.addEventListener('click', function() {
        if (!confirm('Delete this quotation?')) return;
        fetch('/admin/quotations/' + this.dataset.id, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => { if (data.success) window.location.reload(); });
    });
});
</script>
@endpush
