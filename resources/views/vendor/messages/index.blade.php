@extends('vendor.layouts.master')

@section('title', 'Messages - Booking ' . $booking->reference)

@section('content')
<div class="container vendor-page">
    <div class="vendor-page-head">
        <div>
            <h2 class="vendor-page-title">Messages <span style="color:var(--gold-dark);">—</span> Booking {{ $booking->reference }}</h2>
            <div class="vendor-page-sub">Chat with the customer about this booking.</div>
        </div>
        <a href="{{ route('vendor.bookings.index') }}" class="btn-outline-gold" style="padding:8px 18px;font-size:12px;">
            <i class="ti ti-arrow-left"></i> Back to Bookings
        </a>
    </div>

    @include('partials.message-thread', ['booking' => $booking, 'route' => url()->current()])
</div>
@endsection
