@extends('layouts.app')

@section('title', 'My Cart')

@section('content')
<div class="container">
    <h2 class="mb-4">My Cart</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if(count($cart) > 0)
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Type</th>
                                <th>Date</th>
                                <th>Price</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cart as $key => $item)
                                <tr>
                                    <td>{{ $item['name'] }}</td>
                                    <td>{{ str_replace('_', ' ', $item['type']) }}</td>
                                    <td>{{ isset($item['date']) ? \Carbon\Carbon::parse($item['date'])->format('M d, Y') : '—' }}</td>
                                    <td>PKR {{ number_format($item['price']) }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-danger remove-item" data-key="{{ $key }}">Remove</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3">Total</th>
                                <th>PKR {{ number_format(collect($cart)->sum('price')) }}</th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="d-flex justify-content-between">
                    <button class="btn btn-outline-danger" id="clearCart">Clear Cart</button>
                    <a href="{{ route('customer.checkout') }}" class="btn btn-primary">Proceed to Checkout</a>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-info">Your cart is empty. <a href="{{ route('browse.index') }}">Browse services</a></div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.remove-item').forEach(btn => {
    btn.addEventListener('click', function() {
        fetch('/customer/cart/remove/' + this.dataset.key, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => { if (data.success) location.reload(); });
    });
});

document.getElementById('clearCart')?.addEventListener('click', function() {
    fetch('{{ route("customer.cart.clear") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => { if (data.success) location.reload(); });
});
</script>
@endpush
