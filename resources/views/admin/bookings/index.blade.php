@extends('admin.layouts.master')

@section('title', 'All Bookings')

@section('content')
<div class="admin-card">
    <div class="card-header">
        <h5><i class="ti ti-calendar-event"></i> All Bookings</h5>
        <div class="d-flex gap-2">
            <span class="status-badge status-requested">{{ \App\Models\Booking::where('status','requested')->count() }} requested</span>
            <span class="status-badge status-confirmed">{{ \App\Models\Booking::where('status','confirmed')->count() }} confirmed</span>
        </div>
    </div>
    <div class="card-body p-0">
        @if($bookings->count() > 0)
            <table class="table-admin">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Event Date</th>
                        <th>Time</th>
                        <th>Type</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bookings as $booking)
                        <tr>
                            <td><strong>{{ $booking->reference }}</strong>
                                @if($booking->booking_type === 'manual')
                                    <span class="badge bg-secondary" style="font-size:9px;vertical-align:middle;">Manual</span>
                                @endif
                            </td>
                            <td>{{ $booking->customer->name ?? ($booking->booking_type === 'manual' ? 'Manual / Offline' : 'N/A') }}</td>
                            <td>{{ \Carbon\Carbon::parse($booking->event_date)->format('M d, Y') }}</td>
                            <td>{{ $booking->time_slot ? ucfirst($booking->time_slot) : '—' }}</td>
                            <td>{{ ucfirst($booking->event_type) }}</td>
                            <td>{{ $booking->bookingItems->count() }}</td>
                            <td>PKR {{ number_format($booking->total_price) }}</td>
                            <td>
                                <span class="status-badge status-{{ $booking->status }}">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.bookings.show', $booking) }}" class="btn btn-ghost btn-sm">
                                    <i class="ti ti-arrow-right"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="p-3"> {{ $bookings->links() }} </div>
        @else
            <div class="text-center py-5">
                <i class="ti ti-calendar-off" style="font-size:36px;color:var(--text-muted);"></i>
                <p class="text-muted mt-2">No bookings yet.</p>
            </div>
        @endif
    </div>
</div>
@endsection
