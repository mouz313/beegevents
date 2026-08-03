@extends('admin.layouts.master')

@section('title', 'Edit Package')

@section('content')
@php
    $initialItems = $package->packageItems->map(function ($item) {
        return [
            'type' => $item->itemable_type === 'App\Models\HallUnit' ? 'hall_unit' : 'service_listing',
            'id' => (int) $item->itemable_id,
            'label' => $item->itemable
                ? ($item->itemable_type === 'App\Models\HallUnit'
                    ? trim(($item->itemable->hall->name ?? '') . ' - ' . $item->itemable->unit_name, ' -')
                    : $item->itemable->title)
                : 'Item #' . $item->itemable_id,
        ];
    })->values();
@endphp
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
                <div class="col-md-4 mb-3">
                    <label class="form-label" style="font-size:12px;font-weight:600;">Total Price (PKR)</label>
                    <input type="number" step="0.01" class="form-control-admin w-100" name="total_price" min="0" value="{{ $package->total_price }}" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label" style="font-size:12px;font-weight:600;">Duration (Days)</label>
                    <input type="number" class="form-control-admin w-100" name="duration_days" min="1" value="{{ $package->duration_days }}" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label" style="font-size:12px;font-weight:600;">Boost Tier</label>
                    <select class="form-control-admin w-100" name="boost_tier">
                        <option value="">No boost</option>
                        <option value="featured" {{ $package->boost_tier == 'featured' ? 'selected' : '' }}>Featured</option>
                        <option value="premium" {{ $package->boost_tier == 'premium' ? 'selected' : '' }}>Premium</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label" style="font-size:12px;font-weight:600;">Max Halls (leave empty = unlimited)</label>
                    <input type="number" class="form-control-admin w-100" name="max_halls" min="0" value="{{ $package->max_halls }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label" style="font-size:12px;font-weight:600;">Max Services (leave empty = unlimited)</label>
                    <input type="number" class="form-control-admin w-100" name="max_listings" min="0" value="{{ $package->max_listings }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label d-block" style="font-size:12px;font-weight:600;">Status</label>
                    <div class="form-check form-switch" style="padding-top:6px;">
                        <input class="form-check-input" type="checkbox" name="is_active" id="packageActiveSwitch" {{ $package->is_active ? 'checked' : '' }}>
                        <label class="form-check-label" for="packageActiveSwitch" style="font-size:12px;">Active (available for purchase)</label>
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label" style="font-size:12px;font-weight:600;">Hall Units <span style="font-weight:400;color:var(--text-muted);">(combo template)</span></label>
                <select class="form-control-admin w-100" id="hallUnitSelect">
                    <option value="">Select a hall unit...</option>
                    @foreach($hallUnits as $unit)
                        <option value="hall_unit_{{ $unit->id }}">{{ $unit->hall->name }} - {{ $unit->unit_name }} (PKR {{ number_format($unit->base_price) }})</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label" style="font-size:12px;font-weight:600;">Services</label>
                <select class="form-control-admin w-100" id="listingSelect">
                    <option value="">Select a service...</option>
                    @foreach($listings as $listing)
                        <option value="service_listing_{{ $listing->id }}">{{ $listing->title }} (PKR {{ number_format($listing->price) }})</option>
                    @endforeach
                </select>
            </div>
            <label class="form-label" style="font-size:12px;font-weight:600;">Combo Template Items</label>
            <div id="selectedItems" class="p-2 mb-3" style="background:var(--cream);border-radius:8px;min-height:40px;">
                <span class="text-muted" style="font-size:12px;">Template items the vendor will fill with their own items.</span>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-gold" id="updatePackageBtn">Update Package</button>
                <a href="{{ route('admin.packages.index') }}" class="btn btn-ghost">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
let selectedItems = {!! json_encode($initialItems) !!};

const hallUnitMap = {};
document.querySelectorAll('#hallUnitSelect option').forEach(o => {
    const m = o.value.match(/^hall_unit_(\d+)$/);
    if (m) hallUnitMap[parseInt(m[1])] = o.text.split(' (PKR')[0];
});
const listingMap = {};
document.querySelectorAll('#listingSelect option').forEach(o => {
    const m = o.value.match(/^service_listing_(\d+)$/);
    if (m) listingMap[parseInt(m[1])] = o.text.split(' (PKR')[0];
});

function addSelectedItem(type, id) {
    const label = type === 'hall_unit' ? hallUnitMap[id] : listingMap[id];
    if (selectedItems.some(s => s.type === type && s.id === id)) {
        showToast('warning', 'Duplicate', label + ' is already in the package.');
        return;
    }
    selectedItems.push({ type, id, label });
    updateSelectedItems();
}

function updateSelectedItems() {
    if (selectedItems.length === 0) {
        document.getElementById('selectedItems').innerHTML = '<span class="text-muted" style="font-size:12px;">Selected items will appear here.</span>';
        return;
    }
    let html = '<div class="d-flex flex-wrap gap-1">';
    selectedItems.forEach((item, i) => {
        const cls = item.type === 'hall_unit' ? 'background:var(--blue-grey);color:#fff;' : 'background:var(--green);color:#fff;';
        html += `<span style="display:inline-flex;align-items:center;gap:6px;${cls}padding:4px 10px;border-radius:14px;font-size:11px;font-weight:600;margin:2px;">
            <i class="ti ${item.type === 'hall_unit' ? 'ti-building' : 'ti-list-check'}"></i>
            ${item.label || ('#' + item.id)}
            <button type="button" onclick="selectedItems.splice(${i},1);updateSelectedItems();" style="background:none;border:none;color:inherit;cursor:pointer;line-height:1;padding:0;" title="Remove">&times;</button>
        </span>`;
    });
    html += '</div>';
    document.getElementById('selectedItems').innerHTML = html;
}

document.getElementById('hallUnitSelect')?.addEventListener('change', function() {
    if (this.value) {
        const parts = this.value.split('_');
        addSelectedItem('hall_unit', parseInt(parts[2]));
        this.value = '';
    }
});

document.getElementById('listingSelect')?.addEventListener('change', function() {
    if (this.value) {
        const parts = this.value.split('_');
        addSelectedItem('service_listing', parseInt(parts[2]));
        this.value = '';
    }
});

document.getElementById('packageEditForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = document.getElementById('updatePackageBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="ti ti-loader"></i> Updating...';

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
            setTimeout(() => { window.location.href = '{{ route("admin.packages.index") }}'; }, 700);
        } else {
            showToast('error', 'Error', data.message || 'Could not update package.');
            btn.disabled = false;
            btn.innerHTML = 'Update Package';
        }
    })
    .catch(() => {
        showToast('error', 'Error', 'Something went wrong. Please try again.');
        btn.disabled = false;
        btn.innerHTML = 'Update Package';
    });
});

updateSelectedItems();
</script>
@endpush
