@extends('layouts.app')

@section('title', 'Booking Requests')

@section('content')
<div class="container">
    <h2 class="mb-4">Booking Requests</h2>

    <div class="card">
        <div class="card-body">
            @if($items->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
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
                                    <td>#{{ $item->booking_id }}</td>
                                    <td>{{ $item->booking->customer->name ?? 'N/A' }}</td>
                                    <td>{{ str_replace('_', ' ', class_basename($item->itemable_type)) }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item->booking->event_date)->format('M d, Y') }}</td>
                                    <td>PKR {{ number_format($item->price) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $item->vendor_status == 'accepted' ? 'success' : ($item->vendor_status == 'declined' ? 'danger' : 'warning') }}">
                                            {{ ucfirst($item->vendor_status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($item->vendor_status == 'pending')
                                            <button class="btn btn-sm btn-success respond-btn" data-id="{{ $item->id }}" data-status="accepted">Accept</button>
                                            <button class="btn btn-sm btn-danger respond-btn" data-id="{{ $item->id }}" data-status="declined">Decline</button>
                                        @else
                                            <span class="text-muted">Responded</span>
                                        @endif
                                        <a href="{{ route('vendor.messages.index', $item->booking_id) }}" class="btn btn-sm btn-outline-secondary mt-1" style="display:inline-flex;align-items:center;gap:4px;font-size:11px;">
                                            <i class="ti ti-message-2"></i> Chat
                                        </a>
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
