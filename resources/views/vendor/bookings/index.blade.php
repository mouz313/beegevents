@extends('vendor.layouts.master')

@section('title', 'Booking Requests')

@section('content')
<div class="container vendor-page">
    <div class="vendor-page-head">
        <div>
            <h2 class="vendor-page-title">Booking Requests</h2>
            <div class="vendor-page-sub">Accept or decline customer booking requests.</div>
        </div>
        <a href="{{ route('vendor.calendar') }}" class="btn btn-sm btn-gold" style="padding:8px 16px;font-size:12px;">
            <i class="ti ti-plus"></i> Add Manual Booking
        </a>
    </div>

    <div class="profile-card">
        <div class="card-header-custom">
            <div class="card-head-icon"><i class="ti ti-calendar-event"></i></div>
            <h4>Booking <span>Requests</span></h4>
        </div>
        <div class="card-body-custom" style="padding:0;">
            @if($items->count() > 0)
                <div class="table-responsive">
                    <table class="table vendor-table">
                        <thead>
                            <tr>
                                <th>Booking</th>
                                <th>Customer</th>
                                <th>Item</th>
                                <th>Date</th>
                                <th>Price</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $item)
                                <tr id="booking-item-{{ $item->id }}">
                                    <td>
                                        {{ $item->booking->reference }}
                                        @if($item->booking->booking_type === 'manual')
                                            <span class="badge bg-secondary" style="font-size:9px;vertical-align:middle;">Manual</span>
                                        @endif
                                    </td>
                                    <td>{{ $item->booking->customer->name ?? ($item->booking->booking_type === 'manual' ? 'Manual / Offline' : 'N/A') }}</td>
                                    <td>
                                        {{ str_replace('_', ' ', class_basename($item->itemable_type)) }}
                                        @if($item->itemable)
                                            <div style="font-size:11px;color:var(--text-muted);">{{ $item->itemable->unit_name ?? $item->itemable->title ?? '' }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($item->booking->event_date)->format('M d, Y') }}
                                        @if($item->time_slot)
                                            <div style="font-size:11px;color:var(--gold-dark);font-weight:600;">{{ ucfirst($item->time_slot) }}</div>
                                        @endif
                                        @if($item->menuSet)
                                            <div style="font-size:11px;color:var(--text-muted);">Menu: {{ $item->menuSet->name }}</div>
                                        @endif
                                        @if($item->guests)
                                            <div style="font-size:11px;color:var(--text-muted);">Guests: {{ number_format($item->guests) }}</div>
                                        @endif
                                        @if($item->catering_mode)
                                            <div style="font-size:11px;color:var(--text-muted);">Catering: {{ ['internal' => 'In-house', 'external' => 'Outside / third-party', 'none' => 'Self-arrange'][$item->catering_mode] ?? ucfirst($item->catering_mode) }}</div>
                                        @endif
                                        @if(!empty($item->extras))
                                            <div style="font-size:11px;color:var(--text-muted);">
                                                Extras: {{ collect($item->extras)->pluck('name')->implode(', ') }}
                                            </div>
                                        @endif
                                    </td>
                                    <td>PKR {{ number_format($item->price) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $item->vendor_status == 'accepted' ? 'success' : ($item->vendor_status == 'declined' ? 'danger' : 'warning') }}">
                                            {{ ucfirst($item->vendor_status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($item->vendor_status == 'pending')
                                            <button class="btn btn-sm btn-gold respond-btn" data-id="{{ $item->id }}" data-status="accepted" style="padding:6px 14px;font-size:12px;">Accept</button>
                                            <button class="btn btn-sm btn-outline-danger respond-btn" data-id="{{ $item->id }}" data-status="declined" style="padding:6px 14px;font-size:12px;">Decline</button>
                                        @else
                                            <span class="text-muted">Responded</span>
                                        @endif
                                        <a href="{{ route('vendor.messages.index', $item->booking_id) }}" class="btn btn-sm btn-outline-gold mt-1" style="display:inline-flex;align-items:center;gap:4px;font-size:11px;">
                                            <i class="ti ti-message-2"></i> Chat
                                        </a>
                                        <a href="{{ route('vendor.bookings.download', $item->booking_id) }}" target="_blank" class="btn btn-sm btn-outline-gold mt-1" style="display:inline-flex;align-items:center;gap:4px;font-size:11px;">
                                            <i class="ti ti-download"></i> Download
                                        </a>
                                        <a href="{{ route('vendor.bookings.invoice', $item->booking_id) }}" target="_blank" class="btn btn-sm btn-outline-gold mt-1" style="display:inline-flex;align-items:center;gap:4px;font-size:11px;">
                                            <i class="ti ti-file-invoice"></i> Invoice
                                        </a>
                                        @if($item->booking->booking_type === 'manual' && in_array($item->booking->status, ['verified', 'confirmed']))
                                            <form method="POST" action="{{ route('vendor.manual-bookings.destroy', $item->booking_id) }}" onsubmit="return confirm('Cancel this manual booking and release the slot?')" class="mt-1" style="display:inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger" style="padding:4px 10px;font-size:11px;">
                                                    <i class="ti ti-x"></i> Cancel
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $items->links() }}
            @else
                <p class="text-muted mb-0">No booking requests yet.</p>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.respond-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.dataset.id;
        const status = this.dataset.status;
        
        fetch('/vendor/bookings/' + id + '/respond', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ status })
        })
        .then(res => res.json())
        .then(data => { if (data.success) location.reload(); });
    });
});
</script>
@endpush
