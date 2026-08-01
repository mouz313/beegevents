@extends('vendor.layouts.master')

@section('title', 'Manage Halls')

@section('content')
<div class="container vendor-page">
    <div class="vendor-page-head">
        <div>
            <h2 class="vendor-page-title">My Halls</h2>
            <div class="vendor-page-sub">Manage your halls, their floors and units.</div>
        </div>
        <button class="btn-gold" data-bs-toggle="modal" data-bs-target="#hallModal">
            <i class="ti ti-plus"></i> Add New Hall
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row" id="hallsContainer">
        @forelse($halls as $hall)
            <div class="col-md-6 mb-3" id="hall-{{ $hall->id }}">
                <div class="profile-card">
                    <div class="card-body-custom" style="padding:20px;">
                        <div class="d-flex justify-content-between">
                            <h5 style="margin:0;color:var(--charcoal);font-weight:700;font-size:16px;">{{ $hall->name }}</h5>
                            <div>
                                <button class="btn btn-sm btn-outline-gold edit-hall"
                                    data-id="{{ $hall->id }}"
                                    data-name="{{ $hall->name }}"
                                    data-address="{{ $hall->address }}"
                                    data-description="{{ $hall->description ?? '' }}"
                                    data-has_floors="{{ $hall->has_floors }}">Edit</button>
                                <button class="btn btn-sm btn-outline-danger delete-hall" data-id="{{ $hall->id }}">Delete</button>
                            </div>
                        </div>
                        <p class="card-text text-muted">{{ $hall->address }}</p>

                        {{-- Image thumbnails (click to open gallery modal) --}}
                        <div class="hall-images-{{ $hall->id }}" style="display:flex;gap:6px;flex-wrap:wrap;margin-bottom:8px;">
                            @forelse($hall->hallImages as $img)
                                <div style="position:relative;width:60px;height:60px;border-radius:8px;overflow:hidden;border:1px solid var(--border);cursor:pointer;"
                                     data-bs-toggle="modal" data-bs-target="#vendorGallery{{ $hall->id }}">
                                    <img src="{{ asset('storage/' . $img->image_path) }}" style="width:100%;height:100%;object-fit:cover;">
                                    <button class="del-img" data-id="{{ $img->id }}" onclick="event.stopPropagation();"
                                        style="position:absolute;top:2px;right:2px;width:18px;height:18px;border:none;border-radius:50%;background:rgba(192,57,43,0.85);color:#fff;font-size:10px;line-height:1;cursor:pointer;display:flex;align-items:center;justify-content:center;">&times;</button>
                                </div>
                            @empty
                                <div style="font-size:11px;color:var(--text-muted);padding:4px 0;">No images</div>
                            @endforelse
                        </div>
                        <div class="mt-1">
                            <input type="file" class="hall-upload" data-hall-id="{{ $hall->id }}" accept="image/*" style="font-size:11px;width:auto;">
                            <small style="color:var(--text-muted);font-size:10px;">JPEG, PNG, WebP</small>
                        </div>

                        {{-- Floors / Units quick view --}}
                        <div class="mt-2 mb-2" style="font-size:12px;">
                            @if($hall->floors->count() > 0)
                                <strong>Floors:</strong>
                                @foreach($hall->floors as $floor)
                                    <div style="margin-left:12px;padding:2px 0;">
                                        {{ $floor->floor_label }}
                                        <span class="text-muted" style="font-size:11px;">
                                            ({{ $floor->hallUnits->count() }} unit{{ $floor->hallUnits->count() !== 1 ? 's' : '' }}:
                                            @foreach($floor->hallUnits as $u => $unit)
                                                {{ $unit->unit_name }}@if(!$loop->last), @endif
                                            @endforeach
                                            @if($floor->hallUnits->count() === 0)<span class="text-muted">no units</span>@endif
                                        )
                                        </span>
                                    </div>
                                @endforeach
                            @endif
                            @if($hall->floors->count() === 0)
                                <span class="text-muted">No floors configured.</span>
                            @endif
                        </div>

                        <div class="mt-2">
                            <a href="{{ route('vendor.halls.floors.index', $hall) }}" class="btn btn-sm btn-outline-gold">Manage Floors ({{ $hall->floors->count() }})</a>
                            <a href="{{ route('vendor.halls.units.index', $hall) }}" class="btn btn-sm btn-outline-gold">Manage Units ({{ $hall->hallUnits->count() }})</a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Gallery Carousel Modal --}}
            @if($hall->hallImages->count() > 0)
                <div class="modal fade" id="vendorGallery{{ $hall->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content" style="background:#2B2620;border:none;">
                            <div class="modal-body p-0">
                                <div id="vendorCarousel{{ $hall->id }}" class="carousel slide">
                                    <div class="carousel-indicators">
                                        @foreach($hall->hallImages as $i => $img)
                                            <button type="button" data-bs-target="#vendorCarousel{{ $hall->id }}" data-bs-slide-to="{{ $i }}" class="{{ $i === 0 ? 'active' : '' }}"></button>
                                        @endforeach
                                    </div>
                                    <div class="carousel-inner">
                                        @foreach($hall->hallImages as $i => $img)
                                            <div class="carousel-item {{ $i === 0 ? 'active' : '' }}">
                                                <img src="{{ asset('storage/' . $img->image_path) }}" class="d-block w-100" style="max-height:75vh;object-fit:contain;">
                                            </div>
                                        @endforeach
                                    </div>
                                    <button class="carousel-control-prev" type="button" data-bs-target="#vendorCarousel{{ $hall->id }}" data-bs-slide="prev">
                                        <span class="carousel-control-prev-icon"></span>
                                    </button>
                                    <button class="carousel-control-next" type="button" data-bs-target="#vendorCarousel{{ $hall->id }}" data-bs-slide="next">
                                        <span class="carousel-control-next-icon"></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @empty
            <div class="col-12">
                <div class="alert alert-info">No halls yet. Add your first hall!</div>
            </div>
        @endforelse
    </div>
</div>

<!-- Hall Modal -->
<div class="modal fade vendor-modal" id="hallModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="hallForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="hallModalTitle">Add Hall</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="hall_id">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label-custom">Hall Name <span style="color:var(--red);">*</span></label>
                                <input type="text" class="form-control input-custom" id="name" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label-custom">Has Multiple Floors</label>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="has_floors" name="has_floors" value="1" checked>
                                    <label class="form-check-label" for="has_floors">Yes</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label-custom">Address <span style="color:var(--red);">*</span></label>
                        <textarea class="form-control input-custom" id="address" name="address" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label-custom">Description</label>
                        <textarea class="form-control input-custom" id="description" name="description" rows="3" placeholder="Describe your hall, its features, ambiance, etc."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label-custom">Images</label>
                        <input type="file" class="form-control input-custom" id="images" name="images[]" multiple accept="image/jpeg,image/png,image/jpg,image/webp">
                        <small style="color:var(--text-muted);font-size:11px;">JPEG, PNG, WebP. Max 10MB each.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-outline-gold" data-bs-dismiss="modal" style="padding:8px 18px;font-size:13px;">Cancel</button>
                    <button type="submit" class="btn-gold" id="hallSubmitBtn" style="padding:8px 18px;font-size:13px;">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const hallModal = new bootstrap.Modal(document.getElementById('hallModal'));
let editingHallId = null;
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

document.getElementById('hallForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const isEdit = !!editingHallId;
    const url = isEdit ? `/vendor/halls/${editingHallId}` : '{{ route("vendor.halls.store") }}';

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

document.querySelectorAll('.edit-hall').forEach(btn => {
    btn.addEventListener('click', function() {
        editingHallId = this.dataset.id;
        document.getElementById('hallModalTitle').textContent = 'Edit Hall';
        document.getElementById('hall_id').value = this.dataset.id;
        document.getElementById('name').value = this.dataset.name;
        document.getElementById('address').value = this.dataset.address;
        document.getElementById('description').value = this.dataset.description;
        document.getElementById('has_floors').checked = this.dataset.has_floors === '1';
        document.getElementById('hallSubmitBtn').textContent = 'Update';
        hallModal.show();
    });
});

document.querySelectorAll('.delete-hall').forEach(btn => {
    btn.addEventListener('click', function() {
        if (!confirm('Delete this hall?')) return;
        fetch(`/vendor/halls/${this.dataset.id}`, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: new URLSearchParams({ _method: 'DELETE' })
        })
        .then(res => res.json())
        .then(data => { if (data.success) location.reload(); });
    });
});

document.querySelectorAll('.del-img').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.stopPropagation();
        if (!confirm('Delete this image?')) return;
        fetch(`/vendor/halls/images/${this.dataset.id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(data => { if (data.success) this.closest('div[style*="position:relative"]').remove(); });
    });
});

document.getElementById('hallModal').addEventListener('hidden.bs.modal', function() {
    editingHallId = null;
    document.getElementById('hallForm').reset();
    document.getElementById('hallModalTitle').textContent = 'Add Hall';
    document.getElementById('hallSubmitBtn').textContent = 'Save';
});

// Inline image upload per hall card
document.querySelectorAll('.hall-upload').forEach(input => {
    input.addEventListener('change', function() {
        const file = this.files[0];
        if (!file) return;
        const hallId = this.dataset.hallId;
        const formData = new FormData();
        formData.append('image', file);

        fetch(`/vendor/halls/${hallId}/images`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            body: formData,
        })
        .then(res => res.json())
        .then(data => {
            if (!data.success) return;
            const container = document.querySelector('.hall-images-' + hallId);
            const noImg = container.querySelector('div[style*="font-size:11px"]');
            if (noImg) noImg.remove();

            const imgDiv = document.createElement('div');
            imgDiv.style.cssText = 'position:relative;width:60px;height:60px;border-radius:8px;overflow:hidden;border:1px solid var(--border);cursor:pointer;';
            imgDiv.dataset.bsToggle = 'modal';
            imgDiv.dataset.bsTarget = '#vendorGallery' + hallId;
            imgDiv.innerHTML = '<img src="/storage/' + data.image.image_path + '" style="width:100%;height:100%;object-fit:cover;">' +
                '<button class="del-img" data-id="' + data.image.id + '" onclick="event.stopPropagation();" style="position:absolute;top:2px;right:2px;width:18px;height:18px;border:none;border-radius:50%;background:rgba(192,57,43,0.85);color:#fff;font-size:10px;line-height:1;cursor:pointer;display:flex;align-items:center;justify-content:center;">&times;</button>';
            container.appendChild(imgDiv);

            imgDiv.querySelector('.del-img').addEventListener('click', function(e) {
                e.stopPropagation();
                if (!confirm('Delete this image?')) return;
                fetch(`/vendor/halls/images/${this.dataset.id}`, {
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
</script>
@endpush
