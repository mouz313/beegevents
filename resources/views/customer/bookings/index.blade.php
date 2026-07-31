@extends('layouts.app')

@section('title', 'My Bookings')

@section('content')
<div class="container">
    <h2 class="mb-4">My Bookings</h2>

    <div class="card">
        <div class="card-body">
            @if($bookings->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Event Date</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Total</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bookings as $booking)
                                <tr>
                                    <td>#{{ $booking->id }}</td>
                                    <td>{{ \Carbon\Carbon::parse($booking->event_date)->format('M d, Y') }}</td>
                                    <td>{{ ucfirst($booking->event_type) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $booking->status == 'confirmed' ? 'success' : ($booking->status == 'cancelled' ? 'danger' : ($booking->status == 'completed' ? 'info' : 'warning')) }}">
                                            {{ ucfirst($booking->status) }}
                                        </span>
                                    </td>
                                    <td>PKR {{ number_format($booking->total_price) }}</td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('customer.bookings.show', $booking) }}" class="btn btn-sm btn-outline-secondary">View</a>
                                            @if(in_array($booking->status, ['requested', 'discussing', 'verified', 'confirmed']))
                                                <form method="POST" action="{{ route('customer.bookings.cancel', $booking) }}" onsubmit="return confirm('Cancel this booking?')">
                                                    @csrf
                                                    <button class="btn btn-sm btn-outline-danger">Cancel</button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $bookings->links() }}
            @else
                <p class="text-muted mb-0">No bookings yet. <a href="{{ route('browse.index') }}">Browse services</a></p>
            @endif
        </div>
    </div>
</div>
@endsection
