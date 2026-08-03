@extends('customer.layouts.master')

@section('title', 'Messages - Booking ' . $booking->reference)

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 style="color:var(--charcoal);font-weight:700;">Messages — Booking {{ $booking->reference }}</h2>
        <a href="{{ route('customer.bookings.show', $booking) }}" style="font-size:13px;color:var(--gold-dark);">← Back to Booking</a>
    </div>

    @include('partials.message-thread', ['booking' => $booking, 'route' => url()->current()])
</div>
@endsection
