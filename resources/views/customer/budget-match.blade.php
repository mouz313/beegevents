@extends('layouts.app')

@section('title', 'Budget Match')

@section('content')
<div class="container">
    <h2 class="mb-4">Budget-Based Matching</h2>
    <p class="text-muted">Enter your budget and guest count to find the best venue options.</p>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <form id="budgetForm">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Your Budget (PKR)</label>
                            <input type="number" class="form-control" name="budget" min="1" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Number of Guests</label>
                            <input type="number" class="form-control" name="guest_count" min="1" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Event Type (Optional)</label>
                            <select class="form-select" name="event_type">
                                <option value="">Any</option>
                                <option value="wedding">Wedding</option>
                                <option value="engagement">Engagement</option>
                                <option value="corporate">Corporate</option>
                                <option value="birthday">Birthday</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Find Matches</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div id="resultsContainer"></div>
        </div>
    </div>

    <div id="resultsList"></div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('budgetForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);

    document.getElementById('resultsContainer').innerHTML = '<div class="alert alert-info">Searching for matches...</div>';

    fetch('{{ route("customer.budget.match") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        let html = '';
        if (data.results.length === 0) {
            html = '<div class="alert alert-warning">No venues found within your budget.</div>';
        } else {
            html = '<div class="list-group">';
            data.results.forEach(r => {
                html += `
                    <div class="list-group-item ${r.within_budget ? 'list-group-item-success' : ''}">
                        <div class="d-flex justify-content-between">
                            <h6 class="mb-1">${r.unit.hall?.name || ''} - ${r.unit.unit_name}</h6>
                            <strong>PKR ${r.total_estimated.toLocaleString()}</strong>
                        </div>
                        <small>Capacity: ${r.unit.min_capacity}-${r.unit.max_capacity} guests</small>
                        <br>
                        <small>Base: PKR ${r.base_price.toLocaleString()} | Extras: ${r.suggested_extras.length} available</small>
                    </div>
                `;
            });
            html += '</div>';
        }
        document.getElementById('resultsContainer').innerHTML = html;
    });
});
</script>
@endpush
