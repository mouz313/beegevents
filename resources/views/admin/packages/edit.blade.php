@extends('admin.layouts.master')

@section('title', 'Edit Package')

@section('content')
<div class="admin-card" style="max-width:700px;">
    <div class="card-header">
        <h5><i class="ti ti-box"></i> Edit Package: {{ $package->title }}</h5>
        <a href="{{ route('admin.packages.index') }}" class="btn btn-ghost btn-sm">Back</a>
    </div>
    <div class="card-body">
        <form id="packageEditForm">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label" style="font-size:12px;font-weight:600;">Title</label>
                <input type="text" class="form-control-admin w-100" name="title" value="{{ $package->title }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label" style="font-size:12px;font-weight:600;">Description</label>
                <textarea class="form-control-admin w-100" name="description" rows="2">{{ $package->description }}</textarea>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label" style="font-size:12px;font-weight:600;">Total Price</label>
                    <input type="number" step="0.01" class="form-control-admin w-100" name="total_price" min="0" value="{{ $package->total_price }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label" style="font-size:12px;font-weight:600;">Event Type</label>
                    <select class="form-control-admin w-100" name="event_type">
                        <option value="wedding" {{ $package->event_type == 'wedding' ? 'selected' : '' }}>Wedding</option>
                        <option value="engagement" {{ $package->event_type == 'engagement' ? 'selected' : '' }}>Engagement</option>
                        <option value="corporate" {{ $package->event_type == 'corporate' ? 'selected' : '' }}>Corporate</option>
                        <option value="birthday" {{ $package->event_type == 'birthday' ? 'selected' : '' }}>Birthday</option>
                        <option value="other" {{ $package->event_type == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label" style="font-size:12px;font-weight:600;">Hall Units</label>
                <select class="form-control-admin w-100" id="hallUnitSelect">
                    <option value="">Select...</option>
                    @foreach($hallUnits as $unit)
                        <option value="hall_unit_{{ $unit->id }}">{{ $unit->hall->name }} - {{ $unit->unit_name }} (PKR {{ number_format($unit->base_price) }})</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label" style="font-size:12px;font-weight:600;">Services</label>
                <select class="form-control-admin w-100" id="listingSelect">
                    <option value="">Select...</option>
                    @foreach($listings as $listing)
                        <option value="service_listing_{{ $listing->id }}">{{ $listing->title }} (PKR {{ number_format($listing->price) }})</option>
                    @endforeach
                </select>
            </div>
            <div id="selectedItems" class="p-2 mb-3" style="background:var(--cream);border-radius:8px;min-height:40px;">
                <span class="text-muted" style="font-size:12px;">Selected items will appear here.</span>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-gold">Update Package</button>
                <a href="{{ route('admin.packages.index') }}" class="btn btn-ghost">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
let selectedItems = [
    @foreach($package->packageItems as $item)
        { type: '{{ class_basename($item->itemable_type) == "HallUnit" ? "hall_unit" : "service_listing" }}', id: {{ $item->itemable_id }}, label: '{{ $item->itemable?->title ?? $item->itemable?->unit_name ?? "Item #".$item->itemable_id }}' },
    @endforeach
];

function updateSelectedItems() {
    let html = selectedItems.map((item, i) =>
        `<span style="display:inline-block;background:var(--gold);color:white;padding:2px 10px;border-radius:12px;font-size:11px;margin:2px;">${item.type.replace('_',' ')}: ${item.label || '#'+item.id} <button type="button" style="background:none;border:none;color:white;cursor:pointer;margin-left:4px;" onclick="selectedItems.splice(${i},1);updateSelectedItems();">&times;</button></span>`
    ).join('');
    document.getElementById('selectedItems').innerHTML = html || '<span class="text-muted" style="font-size:12px;">Selected items will appear here.</span>';
}

document.getElementById('hallUnitSelect')?.addEventListener('change', function() {
    if (this.value) {
        const parts = this.value.split('_');
        const label = this.options[this.selectedIndex].text.split(' (PKR')[0];
        selectedItems.push({ type: 'hall_unit', id: parseInt(parts[2]), label });
        this.value = '';
        updateSelectedItems();
    }
});

document.getElementById('listingSelect')?.addEventListener('change', function() {
    if (this.value) {
        const parts = this.value.split('_');
        const label = this.options[this.selectedIndex].text.split(' (PKR')[0];
        selectedItems.push({ type: 'service_listing', id: parseInt(parts[2]), label });
        this.value = '';
        updateSelectedItems();
    }
});

document.getElementById('packageEditForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    selectedItems.forEach((item, i) => {
        formData.append(`items[${i}][type]`, item.type);
        formData.append(`items[${i}][id]`, item.id);
    });
    fetch('{{ route("admin.packages.update", $package) }}', {
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
        if (data.success) {
            showToast('success', 'Updated', 'Package updated successfully.');
            setTimeout(() => { window.location.href = '{{ route("admin.packages.index") }}'; }, 500);
        }
    });
});

updateSelectedItems();
</script>
@endpush
