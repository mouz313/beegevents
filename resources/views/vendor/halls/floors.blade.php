@extends('layouts.app')

@section('title', 'Manage Floors - ' . $hall->name)

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>{{ $hall->name }} - Floors</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#floorModal">Add Floor</button>
    </div>

    <div class="row" id="floorsContainer">
        @forelse($floors as $floor)
            <div class="col-md-4 mb-3" id="floor-{{ $floor->id }}">
                <div class="card">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <span class="fw-bold">{{ $floor->floor_label }}</span>
                        <div>
                            <button class="btn btn-sm btn-outline-primary edit-floor" data-id="{{ $floor->id }}" data-label="{{ $floor->floor_label }}">Edit</button>
                            <button class="btn btn-sm btn-outline-danger delete-floor" data-id="{{ $floor->id }}">Delete</button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12"><div class="alert alert-info">No floors added yet.</div></div>
        @endforelse
    </div>
</div>

<div class="modal fade" id="floorModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="floorForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="floorModalTitle">Add Floor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="floor_id">
                    <div class="mb-3">
                        <label class="form-label">Floor Label</label>
                        <input type="text" class="form-control" id="floor_label" name="floor_label" placeholder="e.g. Ground, 1st, 2nd" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="floorSubmitBtn">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const floorModal = new bootstrap.Modal(document.getElementById('floorModal'));
let editingFloorId = null;

document.getElementById('floorForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const isEdit = !!editingFloorId;
    const url = isEdit ? `/vendor/floors/${editingFloorId}` : '{{ route("vendor.halls.floors.store", $hall) }}';
    if (isEdit) formData.append('_method', 'PUT');

    fetch(url, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
        body: formData
    })
    .then(res => res.json())
    .then(data => { if (data.success) location.reload(); });
});

document.querySelectorAll('.edit-floor').forEach(btn => {
    btn.addEventListener('click', function() {
        editingFloorId = this.dataset.id;
        document.getElementById('floorModalTitle').textContent = 'Edit Floor';
        document.getElementById('floor_id').value = this.dataset.id;
        document.getElementById('floor_label').value = this.dataset.label;
        document.getElementById('floorSubmitBtn').textContent = 'Update';
        floorModal.show();
    });
});

document.querySelectorAll('.delete-floor').forEach(btn => {
    btn.addEventListener('click', function() {
        if (!confirm('Delete this floor?')) return;
        fetch(`/vendor/floors/${this.dataset.id}`, {
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

document.getElementById('floorModal').addEventListener('hidden.bs.modal', function() {
    editingFloorId = null;
    document.getElementById('floorForm').reset();
    document.getElementById('floorModalTitle').textContent = 'Add Floor';
    document.getElementById('floorSubmitBtn').textContent = 'Save';
});
</script>
@endpush
