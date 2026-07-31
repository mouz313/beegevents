@extends('layouts.app')

@section('title', 'My Packages')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>My Packages</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#packageModal">Create Package</button>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row">
        @forelse($packages as $package)
            <div class="col-md-6 mb-3" id="package-{{ $package->id }}">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <h5 class="card-title">{{ $package->title }}</h5>
                            <div>
                                <button class="btn btn-sm btn-outline-primary edit-package"
                                    data-id="{{ $package->id }}"
                                    data-title="{{ $package->title }}"
                                    data-description="{{ $package->description ?? '' }}"
                                    data-total_price="{{ $package->total_price }}"
                                    data-event_type="{{ $package->event_type }}"
                                    data-items="{{ $package->packageItems->map(fn($i) => ['type' => $i->itemable_type === 'App\Models\HallUnit' ? 'hall_unit' : 'service_listing', 'id' => $i->itemable_id])->toJson() }}">Edit</button>
                                <button class="btn btn-sm btn-outline-danger delete-package" data-id="{{ $package->id }}">Delete</button>
                            </div>
                        </div>
                        <p class="card-text text-muted">{{ $package->description }}</p>
                        <div class="mb-2">
                            <span class="badge bg-warning text-dark">PKR {{ number_format($package->total_price) }}</span>
                            <span class="badge bg-info">{{ ucfirst($package->event_type) }}</span>
                        </div>
                        <ul class="mb-0" style="font-size:13px;">
                            @foreach($package->packageItems as $item)
                                @if($item->itemable)
                                    <li>
                                        {{ $item->itemable_type === 'App\Models\HallUnit' ? $item->itemable->hall->name.' - '.$item->itemable->unit_name : $item->itemable->title }}
                                        <span class="text-muted">(PKR {{ number_format($item->itemable_type === 'App\Models\HallUnit' ? $item->itemable->base_price : $item->itemable->price) }})</span>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">No packages yet. Create your first package!</div>
            </div>
        @endforelse
    </div>
</div>

<!-- Package Modal -->
<div class="modal fade" id="packageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="packageForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="packageModalTitle">Create Package</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="package_id">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">Package Title</label>
                                <input type="text" class="form-control" id="title" name="title" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Event Type</label>
                                <select class="form-select" id="event_type" name="event_type" required>
                                    <option value="wedding">Wedding</option>
                                    <option value="engagement">Engagement</option>
                                    <option value="corporate">Corporate</option>
                                    <option value="birthday">Birthday</option>
                                    <option value="home">Home</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="2" placeholder="What's included in this package?"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Total Price (PKR)</label>
                        <input type="number" class="form-control" id="total_price" name="total_price" min="0" step="0.01" required>
                        <small style="color:var(--text-muted);font-size:11px;">Tip: sum of selected items below.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Include Items (your halls/units &amp; services)</label>
                        <div id="itemsContainer" style="max-height:280px;overflow-y:auto;border:1px solid var(--border);border-radius:8px;padding:12px;">
                            @forelse($hallUnits as $unit)
                                <div class="form-check">
                                    <input class="form-check-input package-item" type="checkbox" name="items[]" value="hall_unit_{{ $unit->id }}" id="item-unit-{{ $unit->id }}">
                                    <label class="form-check-label" for="item-unit-{{ $unit->id }}">
                                        <i class="ti ti-building"></i> {{ $unit->label }}
                                        <span class="text-muted" style="font-size:11px;">PKR {{ number_format($unit->base_price) }}</span>
                                    </label>
                                </div>
                            @empty
                                <div class="text-muted" style="font-size:12px;">No halls/units yet. <a href="{{ route('vendor.halls.index') }}">Add a hall</a> first.</div>
                            @endforelse
                            @if($hallUnits->isNotEmpty() && $listings->isNotEmpty())
                                <hr>
                            @endif
                            @forelse($listings as $listing)
                                <div class="form-check">
                                    <input class="form-check-input package-item" type="checkbox" name="items[]" value="service_listing_{{ $listing->id }}" id="item-listing-{{ $listing->id }}">
                                    <label class="form-check-label" for="item-listing-{{ $listing->id }}">
                                        <i class="ti ti-list-check"></i> {{ $listing->title }}
                                        <span class="text-muted" style="font-size:11px;">PKR {{ number_format($listing->price) }} / {{ $listing->price_unit }}</span>
                                    </label>
                                </div>
                            @empty
                                @if($hallUnits->isNotEmpty())
                                    <div class="text-muted" style="font-size:12px;">No services yet. <a href="{{ route('vendor.listings.index') }}">Add a service</a> first.</div>
                                @endif
                            @endforelse
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="packageSubmitBtn">Create</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const packageModal = new bootstrap.Modal(document.getElementById('packageModal'));
let editingPackageId = null;
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
const form = document.getElementById('packageForm');
const itemsContainer = document.getElementById('itemsContainer');

function selectedItems() {
    return Array.from(itemsContainer.querySelectorAll('.package-item:checked')).map(cb => {
        const parts = cb.value.split('_');
        return { type: parts.slice(0, 2).join('_'), id: parseInt(parts.slice(2).join(''), 10) };
    });
}

function setItemCheckboxes(items) {
    itemsContainer.querySelectorAll('.package-item').forEach(cb => cb.checked = false);
    items.forEach(item => {
        const needle = item.type === 'hall_unit' ? 'hall_unit_' + item.id : 'service_listing_' + item.id;
        itemsContainer.querySelectorAll('.package-item').forEach(cb => {
            if (cb.value === needle) cb.checked = true;
        });
    });
}

form.addEventListener('submit', function(e) {
    e.preventDefault();
    const isEdit = !!editingPackageId;
    const url = isEdit ? `/vendor/packages/${editingPackageId}` : '{{ route("vendor.packages.store") }}';
    const body = new URLSearchParams(new FormData(this));
    body.delete('items[]');
    selectedItems().forEach((item, i) => {
        body.append('items[' + i + '][type]', item.type);
        body.append('items[' + i + '][id]', item.id);
    });

    if (isEdit) body.append('_method', 'PUT');

    fetch(url, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
        body: body
    })
    .then(res => res.json())
    .then(data => { if (data.success) location.reload(); });
});

document.querySelectorAll('.edit-package').forEach(btn => {
    btn.addEventListener('click', function() {
        editingPackageId = this.dataset.id;
        document.getElementById('packageModalTitle').textContent = 'Edit Package';
        document.getElementById('package_id').value = this.dataset.id;
        document.getElementById('title').value = this.dataset.title;
        document.getElementById('description').value = this.dataset.description;
        document.getElementById('total_price').value = this.dataset.total_price;
        document.getElementById('event_type').value = this.dataset.event_type;
        setItemCheckboxes(JSON.parse(this.dataset.items || '[]'));
        document.getElementById('packageSubmitBtn').textContent = 'Update';
        packageModal.show();
    });
});

document.querySelectorAll('.delete-package').forEach(btn => {
    btn.addEventListener('click', function() {
        if (!confirm('Delete this package?')) return;
        fetch(`/vendor/packages/${this.dataset.id}`, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: new URLSearchParams({ _method: 'DELETE' })
        })
        .then(res => res.json())
        .then(data => { if (data.success) location.reload(); });
    });
});

document.getElementById('packageModal').addEventListener('hidden.bs.modal', function() {
    editingPackageId = null;
    form.reset();
    document.getElementById('packageModalTitle').textContent = 'Create Package';
    document.getElementById('packageSubmitBtn').textContent = 'Create';
});
</script>
@endpush
