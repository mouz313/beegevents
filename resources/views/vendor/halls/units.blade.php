@extends('layouts.app')

@section('title', 'Manage Hall Units - ' . $hall->name)

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>{{ $hall->name }} - Units</h2>
        <div>
            <a href="{{ route('vendor.halls.floors.index', $hall) }}" class="btn btn-outline-info btn-sm">Manage Floors</a>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#unitModal">Add Unit</button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Floor</th>
                    <th>Capacity</th>
                    <th>Base Price</th>
                    <th>Decor</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($units as $unit)
                    <tr id="unit-{{ $unit->id }}">
                        <td>{{ $unit->unit_name }}</td>
                        <td>{{ $unit->floor->floor_label ?? 'N/A' }}</td>
                        <td>{{ $unit->min_capacity }} - {{ $unit->max_capacity }}</td>
                        <td>PKR {{ number_format($unit->base_price) }}</td>
                        <td>{{ ucfirst($unit->decor_type) }}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-info" onclick="showExtras({{ $unit->id }})"><i class="ti ti-package"></i> Extras</button>
                            <button class="btn btn-sm btn-outline-primary edit-unit" data-id="{{ $unit->id }}" data-unit_name="{{ $unit->unit_name }}" data-floor_id="{{ $unit->floor_id }}" data-min_capacity="{{ $unit->min_capacity }}" data-max_capacity="{{ $unit->max_capacity }}" data-menu_summary="{{ $unit->menu_summary }}" data-decor_type="{{ $unit->decor_type }}" data-base_price="{{ $unit->base_price }}">Edit</button>
                            <button class="btn btn-sm btn-outline-danger delete-unit" data-id="{{ $unit->id }}">Delete</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Extra Services Modal -->
<div class="modal fade" id="extrasModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Extra Services — <span id="extrasUnitName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="extrasList" style="margin-bottom:16px;">
                    <p class="text-muted" style="font-size:13px;">Loading...</p>
                </div>
                <hr>
                <h6 style="font-weight:600;">Add Extra Service</h6>
                <form id="extrasForm">
                    @csrf
                    <input type="hidden" id="extras_unit_id">
                    <div class="mb-2">
                        <input type="text" class="form-control" id="extra_name" name="name" placeholder="Service name (e.g. Extra Table)" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <input type="number" step="0.01" class="form-control" id="extra_price" name="price" placeholder="Price" required min="0">
                        </div>
                        <div class="col-md-6 mb-2">
                            <select class="form-select" id="extra_price_unit" name="price_unit">
                                <option value="per_person">Per Person</option>
                                <option value="per_hour">Per Hour</option>
                                <option value="flat">Flat Rate</option>
                                <option value="per_table">Per Table</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-sm btn-primary">Add Service</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Unit Modal -->
<div class="modal fade" id="unitModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="unitForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="unitModalTitle">Add Unit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="unit_id">
                    <div class="mb-3">
                        <label class="form-label">Unit Name</label>
                        <input type="text" class="form-control" id="unit_name" name="unit_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Floor</label>
                        <select class="form-select" id="floor_id" name="floor_id">
                            <option value="">No Floor</option>
                            @foreach($floors as $floor)
                                <option value="{{ $floor->id }}">{{ $floor->floor_label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Min Capacity</label>
                            <input type="number" class="form-control" id="min_capacity" name="min_capacity" min="1" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Max Capacity</label>
                            <input type="number" class="form-control" id="max_capacity" name="max_capacity" min="1" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Base Price (PKR)</label>
                        <input type="number" step="0.01" class="form-control" id="base_price" name="base_price" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Decor Type</label>
                        <select class="form-select" id="decor_type" name="decor_type">
                            <option value="fixed">Fixed</option>
                            <option value="outsourced">Outsourced</option>
                            <option value="customizable">Customizable</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Menu Summary</label>
                        <textarea class="form-control" id="menu_summary" name="menu_summary" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="unitSubmitBtn">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const unitModal = new bootstrap.Modal(document.getElementById('unitModal'));
const extrasModal = new bootstrap.Modal(document.getElementById('extrasModal'));
let editingUnitId = null;

function showExtras(unitId) {
    const unitName = document.querySelector(`#unit-${unitId} td:first-child`).textContent;
    document.getElementById('extrasUnitName').textContent = unitName;
    document.getElementById('extras_unit_id').value = unitId;
    document.getElementById('extrasList').innerHTML = '<p class="text-muted" style="font-size:13px;">Loading...</p>';
    extrasModal.show();

    fetch(`/vendor/halls/{{ $hall->id }}/units/${unitId}/extras`, {
        headers: {'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest'}
    })
    .then(r => r.json())
    .then(data => {
        let html = '';
        if (data.services.length === 0) html = '<p class="text-muted" style="font-size:13px;">No extra services added yet.</p>';
        data.services.forEach(s => {
            html += `<div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid var(--border);font-size:13px;">
                <div><strong>${s.name}</strong> <span class="text-muted">(PKR ${Number(s.price).toLocaleString()} / ${s.price_unit.replace('_',' ')})</span></div>
                <button class="btn btn-sm btn-outline-danger" onclick="deleteExtra(${s.id})">&times;</button>
            </div>`;
        });
        document.getElementById('extrasList').innerHTML = html;
    });
}

function deleteExtra(id) {
    if (!confirm('Remove this extra service?')) return;
    fetch(`/vendor/extra-services/${id}`, {
        method: 'POST',
        headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json'},
        body: new URLSearchParams({_method: 'DELETE'})
    }).then(r => r.json()).then(d => { if (d.success) showExtras(document.getElementById('extras_unit_id').value); });
}

document.getElementById('extrasForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const unitId = document.getElementById('extras_unit_id').value;
    const formData = new FormData(this);
    fetch(`/vendor/halls/{{ $hall->id }}/units/${unitId}/extras`, {
        method: 'POST',
        headers: {'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json'},
        body: formData
    }).then(r => r.json()).then(d => {
        if (d.success) {
            this.reset();
            showExtras(unitId);
        }
    });
});

document.getElementById('unitForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const isEdit = !!editingUnitId;
    const url = isEdit ? `/vendor/hall-units/${editingUnitId}` : '{{ route("vendor.halls.units.store", $hall) }}';
    
    if (isEdit) formData.append('_method', 'PUT');

    fetch(url, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) location.reload();
    });
});

document.querySelectorAll('.edit-unit').forEach(btn => {
    btn.addEventListener('click', function() {
        editingUnitId = this.dataset.id;
        document.getElementById('unitModalTitle').textContent = 'Edit Unit';
        document.getElementById('unit_id').value = this.dataset.id;
        document.getElementById('unit_name').value = this.dataset.unit_name;
        document.getElementById('floor_id').value = this.dataset.floor_id;
        document.getElementById('min_capacity').value = this.dataset.min_capacity;
        document.getElementById('max_capacity').value = this.dataset.max_capacity;
        document.getElementById('base_price').value = this.dataset.base_price;
        document.getElementById('decor_type').value = this.dataset.decor_type;
        document.getElementById('menu_summary').value = this.dataset.menu_summary;
        document.getElementById('unitSubmitBtn').textContent = 'Update';
        unitModal.show();
    });
});

document.querySelectorAll('.delete-unit').forEach(btn => {
    btn.addEventListener('click', function() {
        if (!confirm('Delete this unit?')) return;
        fetch(`/vendor/hall-units/${this.dataset.id}`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: new URLSearchParams({ _method: 'DELETE' })
        })
        .then(res => res.json())
        .then(data => { if (data.success) location.reload(); });
    });
});

document.getElementById('unitModal').addEventListener('hidden.bs.modal', function() {
    editingUnitId = null;
    document.getElementById('unitForm').reset();
    document.getElementById('unitModalTitle').textContent = 'Add Unit';
    document.getElementById('unitSubmitBtn').textContent = 'Save';
});
</script>
@endpush
