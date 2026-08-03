@extends('admin.layouts.master')

@section('title', 'Messages - Booking ' . $booking->reference)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 style="margin:0;">Messages — Booking {{ $booking->reference }}</h2>
    <a href="{{ route('admin.bookings.show', $booking) }}" class="btn btn-outline-secondary btn-sm">← Back to Booking</a>
</div>

@include('partials.message-thread', ['booking' => $booking, 'route' => url()->current()])
@endsection
