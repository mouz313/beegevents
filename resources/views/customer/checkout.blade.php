@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="container">
    <h2 class="mb-4">Checkout</h2>

    <div class="row">
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">Booking Details</h5></div>
                <div class="card-body">
                    <form id="checkoutForm">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Event Date</label>
                            <input type="date" class="form-control" name="event_date" min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Event Type</label>
                            <select class="form-select" name="event_type" required>
                                <option value="wedding">Wedding</option>
                                <option value="engagement">Engagement</option>
                                <option value="corporate">Corporate</option>
                                <option value="birthday">Birthday</option>
                                <option value="home">Home Event</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Notes (Optional)</label>
                            <textarea class="form-control" name="notes" rows="3" placeholder="Any special requirements..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg w-100">Place Booking Request</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">Order Summary</h5></div>
                <div class="card-body">
                    @foreach($cart as $item)
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ $item['name'] }}</span>
                            <span>PKR {{ number_format($item['price']) }}</span>
                        </div>
                    @endforeach
                    <hr>
                    <div class="d-flex justify-content-between fw-bold">
                        <span>Total</span>
                        <span>PKR {{ number_format(collect($cart)->sum('price')) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('checkoutForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch('{{ route("customer.bookings.store") }}', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            window.location.href = '/customer/bookings/' + data.booking_id;
        } else if (data.message) {
            alert(data.message);
        }
    })
    .catch(err => console.error(err));
});
</script>
@endpush
