@extends('admin.layouts.master')

@section('title', 'Edit Vendor')

@section('content')
<div class="row g-4">
    {{-- Vendor Info Section --}}
    <div class="col-lg-7">
        <div class="admin-card">
            <div class="card-header">
                <h5><i class="ti ti-building-store"></i> Edit Vendor: {{ $vendorProfile->business_name }}</h5>
                <a href="{{ route('admin.vendors.show', $vendorProfile) }}" class="btn btn-ghost btn-sm">Back</a>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.vendors.update', $vendorProfile) }}" enctype="multipart/form-data">
                    @csrf @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;color:var(--text-muted);">Business Name</label>
                            <input type="text" class="form-control-admin w-100" name="business_name" value="{{ old('business_name', $vendorProfile->business_name) }}" required style="margin-top:4px;">
                        </div>
                        <div class="col-md-6">
                            <label style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;color:var(--text-muted);">Vendor Type</label>
                            <select class="form-control-admin w-100" name="vendor_type" id="adminVendorType" required style="margin-top:4px;">
                                @foreach(config('vendor-specs.types') as $tKey => $tDef)
                                    <option value="{{ $tKey }}" {{ old('vendor_type', $vendorProfile->vendor_type) == $tKey ? 'selected' : '' }}>{{ $tDef['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;color:var(--text-muted);">City</label>
                            <input type="text" class="form-control-admin w-100" name="city" value="{{ old('city', $vendorProfile->city) }}" required style="margin-top:4px;">
                        </div>
                        <div class="col-md-6">
                            <label style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;color:var(--text-muted);">Phone</label>
                            <input type="text" class="form-control-admin w-100" name="phone" value="{{ old('phone', $vendorProfile->phone) }}" placeholder="03XX-XXXXXXX" style="margin-top:4px;">
                        </div>
                        <div class="col-12">
                            <label style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;color:var(--text-muted);">Address</label>
                            <input type="text" class="form-control-admin w-100" name="address" value="{{ old('address', $vendorProfile->address) }}" placeholder="Full address" style="margin-top:4px;">
                        </div>
                        <div class="col-md-6">
                            <label style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;color:var(--text-muted);">Status</label>
                            <select class="form-control-admin w-100" name="status" required style="margin-top:4px;">
                                <option value="pending" {{ old('status', $vendorProfile->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="verified" {{ old('status', $vendorProfile->status) == 'verified' ? 'selected' : '' }}>Verified</option>
                                <option value="suspended" {{ old('status', $vendorProfile->status) == 'suspended' ? 'selected' : '' }}>Suspended</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;color:var(--text-muted);">Logo / Avatar</label>
                            <input type="file" class="form-control-admin w-100" name="logo" accept="image/*" style="margin-top:4px;">
                            @if($vendorProfile->logo_path)
                                <div class="mt-2 d-flex align-items-center gap-2">
                                    <img src="{{ asset('storage/' . $vendorProfile->logo_path) }}" style="width:36px;height:36px;border-radius:50%;object-fit:cover;">
                                    <span style="font-size:11px;color:var(--text-muted);">Current logo</span>
                                </div>
                            @endif
                        </div>
                        <div class="col-12">
                            <label style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;color:var(--text-muted);">Cancellation Policy</label>
                            <textarea class="form-control-admin w-100" name="cancellation_policy" rows="3" style="margin-top:4px;">{{ old('cancellation_policy', $vendorProfile->cancellation_policy) }}</textarea>
                        </div>
                    </div>

                    {{-- Business Details (contact/legal + type-specific specs) --}}
                    <hr style="border-color:var(--border);margin:20px 0;">
                    <h6 style="font-weight:700;font-size:14px;margin-bottom:4px;"><i class="ti ti-settings"></i> Business Details</h6>
                    <p class="text-muted" style="font-size:12px;margin-bottom:14px;">These are the type-specific fields the vendor filled during registration.</p>
                    <style>:root { --light-honey: #FAEEDA; }</style>

                    @include('vendor.partials.contact-legal', ['values' => $vendorProfile->specFormValues()])

                    @php $specValues = $vendorProfile->specFormValues(); $selectedType = $vendorProfile->vendor_type; @endphp
                    @foreach(config('vendor-specs.types') as $tKey => $tDef)
                        @include('vendor.partials.type-specs', [
                            'typeKey' => $tKey,
                            'values' => $specValues,
                            'selected' => $selectedType,
                        ])
                    @endforeach

                    <div class="d-flex gap-2 pt-3 mt-3 border-top">
                        <button type="submit" class="btn btn-gold"><i class="ti ti-device-floppy"></i> Update Vendor</button>
                        <a href="{{ route('admin.vendors.show', $vendorProfile) }}" class="btn btn-ghost">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Halls with Images + Floors/Units CRUD --}}
    <div class="col-lg-5">
        <div class="admin-card">
            <div class="card-header">
                <h5><i class="ti ti-building"></i> Halls ({{ $vendorProfile->halls->count() }})</h5>
            </div>
            <div class="card-body p-0">
                @forelse($vendorProfile->halls as $hall)
                    <div class="hall-block-{{ $hall->id }}" style="padding:16px 20px;{{ !$loop->last ? 'border-bottom:1px solid var(--border);' : '' }}">
                        {{-- Hall Header --}}
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <strong style="font-size:14px;">{{ $hall->name }}</strong>
                                <div style="font-size:11px;color:var(--text-muted);"><i class="ti ti-map-pin"></i> {{ $hall->address }}</div>
                            </div>
                            <span style="font-size:11px;color:var(--text-muted);white-space:nowrap;">{{ $hall->hallUnits->count() }} unit(s)</span>
                        </div>

                        {{-- Images --}}
                        <div class="hall-images-{{ $hall->id }}" style="display:flex;gap:6px;flex-wrap:wrap;margin-bottom:8px;">
                            @forelse($hall->hallImages as $img)
                                <div style="position:relative;width:64px;height:64px;border-radius:8px;overflow:hidden;border:1px solid var(--border);flex-shrink:0;">
                                    <img src="{{ asset('storage/' . $img->image_path) }}" style="width:100%;height:100%;object-fit:cover;">
                                    <button type="button" class="del-hall-img" data-id="{{ $img->id }}"
                                        style="position:absolute;top:2px;right:2px;width:18px;height:18px;border:none;border-radius:50%;background:rgba(192,57,43,0.85);color:#fff;font-size:10px;line-height:1;cursor:pointer;display:flex;align-items:center;justify-content:center;">&times;</button>
                                </div>
                            @empty
                                <div style="font-size:11px;color:var(--text-muted);padding:4px 0;">No images</div>
                            @endforelse
                        </div>

                        {{-- Upload form --}}
                        <div class="mb-2">
                            <input type="file" class="hall-img-input" data-hall-id="{{ $hall->id }}" accept="image/*" style="font-size:11px;width:auto;">
                            <span style="font-size:10px;color:var(--text-muted);margin-left:4px;">JPEG, PNG, WebP</span>
                        </div>

                        {{-- Floors Accordion --}}
                        <div class="accordion accordion-flush floors-accordion" id="floorAccordion{{ $hall->id }}">
                            @foreach($hall->floors as $f => $floor)
                                <div class="accordion-item floor-item" data-floor-id="{{ $floor->id }}" style="border:1px solid var(--border);margin-bottom:6px;border-radius:8px;overflow:hidden;">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button {{ $f > 0 ? 'collapsed' : '' }}" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#editFloor-{{ $hall->id }}-{{ $floor->id }}"
                                                style="font-size:12px;font-weight:600;padding:8px 12px;">
                                            {{ $floor->floor_label }}
                                            <span class="badge bg-secondary ms-2" style="font-size:9px;">{{ $floor->hallUnits->count() }} unit(s)</span>
                                            <button type="button" class="del-floor-btn ms-auto" data-id="{{ $floor->id }}"
                                                    style="background:none;border:none;color:#c0392b;font-size:14px;line-height:1;padding:0 4px;" title="Delete floor">&times;</button>
                                        </button>
                                    </h2>
                                    <div id="editFloor-{{ $hall->id }}-{{ $floor->id }}" class="accordion-collapse collapse {{ $f === 0 ? 'show' : '' }}">
                                        <div class="accordion-body p-0">
                                            {{-- Units table --}}
                                            <div class="units-container" data-floor-id="{{ $floor->id }}">
                                                @if($floor->hallUnits->count() > 0)
                                                    <table class="table-admin" style="margin:0;font-size:11px;">
                                                        <thead>
                                                            <tr><th>Name</th><th>Capacity</th><th>Price</th><th></th></tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($floor->hallUnits as $unit)
                                                                <tr class="unit-row" data-unit-id="{{ $unit->id }}">
                                                                    <td>{{ $unit->unit_name }}</td>
                                                                    <td>{{ $unit->min_capacity ?? '—' }}–{{ $unit->max_capacity ?? '—' }}</td>
                                                                    <td>PKR {{ number_format($unit->base_price) }}</td>
                                                                    <td style="text-align:right;">
                                                                        <button type="button" class="del-unit-btn" data-id="{{ $unit->id }}"
                                                                                style="background:none;border:none;color:#c0392b;font-size:12px;cursor:pointer;">&times;</button>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                @endif
                                            </div>

                                            {{-- Add Unit inline form --}}
                                            <div class="add-unit-form" data-floor-id="{{ $floor->id }}" data-hall-id="{{ $hall->id }}"
                                                 style="border-top:1px solid var(--border);padding:8px 10px;background:var(--cream);">
                                                <div class="row g-1 align-items-end">
                                                    <div class="col-3"><input type="text" class="form-control form-control-sm unit-name" placeholder="Name" style="font-size:11px;"></div>
                                                    <div class="col-2"><input type="number" class="form-control form-control-sm unit-min" placeholder="Min" style="font-size:11px;"></div>
                                                    <div class="col-2"><input type="number" class="form-control form-control-sm unit-max" placeholder="Max" style="font-size:11px;"></div>
                                                    <div class="col-3"><input type="number" class="form-control form-control-sm unit-price" placeholder="Price" style="font-size:11px;"></div>
                                                    <div class="col-2"><button type="button" class="btn btn-gold btn-sm w-100 add-unit-btn" style="font-size:11px;">Add</button></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Add Floor inline form --}}
                        <div class="d-flex gap-2 mt-2">
                            <input type="text" class="form-control form-control-sm floor-label-input" placeholder="New floor name..." style="font-size:11px;">
                            <button type="button" class="btn btn-gold btn-sm add-floor-btn" data-hall-id="{{ $hall->id }}" style="font-size:11px;white-space:nowrap;">Add Floor</button>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4">
                        <i class="ti ti-building-off" style="font-size:28px;color:var(--text-muted);opacity:0.4;"></i>
                        <p class="text-muted mt-2" style="font-size:13px;">No halls for this vendor.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Toggle type-spec groups when vendor type changes
(function() {
    const typeSelect = document.getElementById('adminVendorType');
    if (!typeSelect) return;
    const updateSpecGroups = () => {
        const val = typeSelect.value;
        document.querySelectorAll('.spec-group').forEach(g => {
            g.style.display = g.dataset.specGroup === val ? '' : 'none';
        });
    };
    typeSelect.addEventListener('change', updateSpecGroups);
    updateSpecGroups();
})();

const BASE = '{{ url("/admin/vendors/halls") }}';
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

// ===== Image Upload =====
document.querySelectorAll('.del-hall-img').forEach(btn => {
    btn.addEventListener('click', function() {
        if (!confirm('Delete this image?')) return;
        fetch(BASE + '/images/' + this.dataset.id, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(data => { if (data.success) this.closest('div[style*="position:relative"]').remove(); });
    });
});

document.querySelectorAll('.hall-img-input').forEach(input => {
    input.addEventListener('change', function() {
        const file = this.files[0];
        if (!file) return;
        const hallId = this.dataset.hallId;
        const formData = new FormData();
        formData.append('image', file);

        fetch(BASE + '/' + hallId + '/images', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            body: formData,
        })
        .then(res => res.json())
        .then(data => {
            if (!data.success) return;
            const container = this.closest('div').previousElementSibling;
            const noImg = container.querySelector('div[style*="font-size:11px"]');
            if (noImg) noImg.remove();

            const imgDiv = document.createElement('div');
            imgDiv.style.cssText = 'position:relative;width:64px;height:64px;border-radius:8px;overflow:hidden;border:1px solid var(--border);flex-shrink:0;';
            imgDiv.innerHTML = '<img src="/storage/' + data.image.image_path + '" style="width:100%;height:100%;object-fit:cover;">' +
                '<button type="button" class="del-hall-img" data-id="' + data.image.id + '" style="position:absolute;top:2px;right:2px;width:18px;height:18px;border:none;border-radius:50%;background:rgba(192,57,43,0.85);color:#fff;font-size:10px;line-height:1;cursor:pointer;display:flex;align-items:center;justify-content:center;">&times;</button>';
            container.appendChild(imgDiv);

            imgDiv.querySelector('.del-hall-img').addEventListener('click', function() {
                if (!confirm('Delete this image?')) return;
                fetch(BASE + '/images/' + this.dataset.id, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                })
                .then(res => res.json())
                .then(d => { if (d.success) imgDiv.remove(); });
            });

            this.value = '';
        });
    });
});

// ===== Floor CRUD =====
document.querySelectorAll('.add-floor-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const hallId = this.dataset.hallId;
        const input = this.closest('.d-flex').querySelector('.floor-label-input');
        const label = input.value.trim();
        if (!label) { alert('Enter a floor name.'); return; }

        fetch('/admin/halls/' + hallId + '/floors', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'Content-Type': 'application/json' },
            body: JSON.stringify({ floor_label: label })
        })
        .then(res => res.json())
        .then(data => {
            if (!data.success) return;
            input.value = '';
            const accordion = document.querySelector('#floorAccordion' + hallId);
            const floor = data.floor;
            const fId = floor.id;
            const empty = accordion.querySelector('.accordion-item') === null;
            const item = document.createElement('div');
            item.className = 'accordion-item floor-item';
            item.dataset.floorId = fId;
            item.style.cssText = 'border:1px solid var(--border);margin-bottom:6px;border-radius:8px;overflow:hidden;';
            item.innerHTML =
                '<h2 class="accordion-header">' +
                    '<button class="accordion-button ' + (empty ? '' : 'collapsed') + '" type="button" data-bs-toggle="collapse" data-bs-target="#editFloor-' + hallId + '-' + fId + '" style="font-size:12px;font-weight:600;padding:8px 12px;">' +
                        floor.floor_label +
                        '<span class="badge bg-secondary ms-2" style="font-size:9px;">0 unit(s)</span>' +
                        '<button type="button" class="del-floor-btn ms-auto" data-id="' + fId + '" style="background:none;border:none;color:#c0392b;font-size:14px;line-height:1;padding:0 4px;" title="Delete floor">&times;</button>' +
                    '</button>' +
                '</h2>' +
                '<div id="editFloor-' + hallId + '-' + fId + '" class="accordion-collapse collapse ' + (empty ? 'show' : '') + '">' +
                    '<div class="accordion-body p-0">' +
                        '<div class="units-container" data-floor-id="' + fId + '"></div>' +
                        '<div class="add-unit-form" data-floor-id="' + fId + '" data-hall-id="' + hallId + '" style="border-top:1px solid var(--border);padding:8px 10px;background:var(--cream);">' +
                            '<div class="row g-1 align-items-end">' +
                                '<div class="col-3"><input type="text" class="form-control form-control-sm unit-name" placeholder="Name" style="font-size:11px;"></div>' +
                                '<div class="col-2"><input type="number" class="form-control form-control-sm unit-min" placeholder="Min" style="font-size:11px;"></div>' +
                                '<div class="col-2"><input type="number" class="form-control form-control-sm unit-max" placeholder="Max" style="font-size:11px;"></div>' +
                                '<div class="col-3"><input type="number" class="form-control form-control-sm unit-price" placeholder="Price" style="font-size:11px;"></div>' +
                                '<div class="col-2"><button type="button" class="btn btn-gold btn-sm w-100 add-unit-btn" style="font-size:11px;">Add</button></div>' +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                '</div>';
            accordion.appendChild(item);

            item.querySelector('.del-floor-btn').addEventListener('click', deleteFloorHandler);
            item.querySelector('.add-unit-btn').addEventListener('click', addUnitHandler);

            const accordionEl = new bootstrap.Collapse(document.getElementById('editFloor-' + hallId + '-' + fId), { toggle: false });
            if (empty) accordionEl.show();
        });
    });
});

function deleteFloorHandler() {
    if (!confirm('Delete this floor and all its units?')) return;
    const floorId = this.dataset.id;
    const item = this.closest('.floor-item');
    fetch('/admin/floors/' + floorId, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(res => res.json())
    .then(data => { if (data.success) item.remove(); });
}

document.querySelectorAll('.del-floor-btn').forEach(btn => {
    btn.addEventListener('click', deleteFloorHandler);
});

// ===== Unit CRUD =====
function addUnitHandler() {
    const form = this.closest('.add-unit-form');
    const floorId = form.dataset.floorId;
    const hallId = form.dataset.hallId;
    const name = form.querySelector('.unit-name').value.trim();
    const minCap = form.querySelector('.unit-min').value;
    const maxCap = form.querySelector('.unit-max').value;
    const price = form.querySelector('.unit-price').value;
    if (!name) { alert('Enter a unit name.'); return; }

    fetch('/admin/halls/' + hallId + '/units', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'Content-Type': 'application/json' },
        body: JSON.stringify({ floor_id: floorId, unit_name: name, min_capacity: minCap || null, max_capacity: maxCap || null, base_price: price || null })
    })
    .then(res => res.json())
    .then(data => {
        if (!data.success) return;
        const container = form.closest('.accordion-body').querySelector('.units-container');
        const unit = data.unit;
        let table = container.querySelector('table');
        if (!table) {
            table = document.createElement('table');
            table.className = 'table-admin';
            table.style.cssText = 'margin:0;font-size:11px;';
            table.innerHTML = '<thead><tr><th>Name</th><th>Capacity</th><th>Price</th><th></th></tr></thead><tbody></tbody>';
            container.appendChild(table);
        }
        const tbody = table.querySelector('tbody');
        const row = document.createElement('tr');
        row.className = 'unit-row';
        row.dataset.unitId = unit.id;
        row.innerHTML =
            '<td>' + unit.unit_name + '</td>' +
            '<td>' + (unit.min_capacity || '—') + '–' + (unit.max_capacity || '—') + '</td>' +
            '<td>PKR ' + Number(unit.base_price).toLocaleString() + '</td>' +
            '<td style="text-align:right;"><button type="button" class="del-unit-btn" data-id="' + unit.id + '" style="background:none;border:none;color:#c0392b;font-size:12px;cursor:pointer;">&times;</button></td>';
        tbody.appendChild(row);

        row.querySelector('.del-unit-btn').addEventListener('click', deleteUnitHandler);
        form.querySelector('.unit-name').value = '';
        form.querySelector('.unit-min').value = '';
        form.querySelector('.unit-max').value = '';
        form.querySelector('.unit-price').value = '';

        const badge = row.closest('.accordion-item').querySelector('.badge');
        if (badge) badge.textContent = tbody.querySelectorAll('tr').length + ' unit(s)';
    });
}

function deleteUnitHandler() {
    if (!confirm('Delete this unit?')) return;
    const unitId = this.dataset.id;
    const row = this.closest('.unit-row');
    fetch('/admin/hall-units/' + unitId, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(res => res.json())
    .then(data => {
        if (!data.success) return;
        const table = row.closest('table');
        row.remove();
        const badge = row.closest('.accordion-item').querySelector('.badge');
        if (badge) {
            const remaining = table.querySelectorAll('tbody tr').length;
            badge.textContent = remaining + ' unit(s)';
            if (remaining === 0) table.remove();
        }
    });
}

document.querySelectorAll('.add-unit-btn').forEach(btn => {
    btn.addEventListener('click', addUnitHandler);
});

document.querySelectorAll('.del-unit-btn').forEach(btn => {
    btn.addEventListener('click', deleteUnitHandler);
});
</script>
@endpush
