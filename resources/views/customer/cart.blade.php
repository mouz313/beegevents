@extends('customer.layouts.master')

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
                                <th>Details</th>
                                <th>Price</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cart as $key => $item)
                                <tr>
                                    <td>{{ $item['name'] }}</td>
                                    <td>{{ str_replace('_', ' ', $item['type']) }}</td>
                                    <td>
                                        {{ isset($item['date']) ? \Carbon\Carbon::parse($item['date'])->format('M d, Y') : '—' }}
                                        @if(isset($item['time_slot']))
                                            <div style="font-size:11px;color:var(--text-muted);">{{ ucfirst($item['time_slot']) }}</div>
                                        @endif
                                    </td>
                                    <td style="font-size:12px;">
                                        @if(isset($item['menu_set_name']) && $item['menu_set_name'])
                                            <span class="badge bg-primary">Menu: {{ $item['menu_set_name'] }} (+PKR {{ number_format($item['menu_set_price'] ?? 0) }})</span>
                                        @endif
                                        @if(isset($item['guests']) && $item['guests'])
                                            <div style="color:var(--text-muted);">Guests: {{ number_format($item['guests']) }}</div>
                                        @endif
                                        @if(isset($item['catering_mode']) && $item['catering_mode'])
                                            <div style="color:var(--text-muted);">Catering: {{ ['internal' => 'In-house', 'external' => 'Outside / third-party', 'none' => 'Self-arrange'][$item['catering_mode']] ?? ucfirst($item['catering_mode']) }}</div>
                                        @endif
                                        @if(!empty($item['extras']))
                                            <div style="color:var(--text-muted);">{{ count($item['extras']) }} extra(s): {{ collect($item['extras'])->pluck('name')->implode(', ') }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        PKR {{ number_format($item['price'] + ($item['extras_total'] ?? 0) + ($item['menu_set_price'] ?? 0)) }}
                                        @if(isset($item['menu_set_price']) && $item['menu_set_price'] > 0)
                                            <div style="font-size:11px;color:var(--text-muted);">incl. PKR {{ number_format($item['menu_set_price']) }} menu</div>
                                        @endif
                                        @if(!empty($item['extras_total']))
                                            <div style="font-size:11px;color:var(--text-muted);">incl. PKR {{ number_format($item['extras_total']) }} extras</div>
                                        @endif
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-danger remove-item" data-key="{{ $key }}">Remove</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4">Total</th>
                                <th>PKR {{ number_format(collect($cart)->sum(fn($i) => $i['price'] + ($i['extras_total'] ?? 0) + ($i['menu_set_price'] ?? 0))) }}</th>
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
