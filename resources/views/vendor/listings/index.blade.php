@extends('vendor.layouts.master')

@section('title', 'Manage Services')

@section('content')
<div class="container vendor-page">
    <div class="vendor-page-head">
        <div>
            <h2 class="vendor-page-title">My Service Listings</h2>
            <div class="vendor-page-sub">Offer standalone services for customers to book.</div>
        </div>
        <button class="btn-gold" data-bs-toggle="modal" data-bs-target="#listingModal">
            <i class="ti ti-plus"></i> Add Service
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="profile-card">
        <div class="card-header-custom compact">
            <div class="card-head-icon"><i class="ti ti-list-check"></i></div>
            <h4>Service <span>Listings</span></h4>
        </div>
        <div class="card-body-custom" style="padding:0;">
            <div class="table-responsive">
                <table class="table vendor-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Unit</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                @forelse($listings as $listing)
                    <tr id="listing-{{ $listing->id }}">
                        <td>{{ $listing->title }}</td>
                        <td>{{ $listing->serviceCategory->name ?? 'N/A' }}</td>
                        <td>PKR {{ number_format($listing->price) }}</td>
                        <td>{{ str_replace('_', ' ', ucfirst($listing->price_unit)) }}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-gold edit-listing" data-id="{{ $listing->id }}" data-category_id="{{ $listing->service_category_id }}" data-title="{{ $listing->title }}" data-description="{{ $listing->description }}" data-price="{{ $listing->price }}" data-price_unit="{{ $listing->price_unit }}">Edit</button>
                            <button class="btn btn-sm btn-outline-danger delete-listing" data-id="{{ $listing->id }}">Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted">No services listed yet.</td></tr>
                @endforelse
                </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade vendor-modal" id="listingModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="listingForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="listingModalTitle">Add Service</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="listing_id">
                    <div class="mb-3">
                        <label class="form-label-custom">Category <span style="color:var(--red);">*</span></label>
                        <select class="form-select input-custom" id="service_category_id" name="service_category_id" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label-custom">Title <span style="color:var(--red);">*</span></label>
                        <input type="text" class="form-control input-custom" id="title" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label-custom">Description</label>
                        <textarea class="form-control input-custom" id="description" name="description" rows="2"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label-custom">Price <span style="color:var(--red);">*</span></label>
                            <input type="number" step="0.01" class="form-control input-custom" id="price" name="price" min="0" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label-custom">Price Unit</label>
                            <select class="form-select input-custom" id="price_unit" name="price_unit">
                                <option value="fixed">Fixed</option>
                                <option value="per_person">Per Person</option>
                                <option value="per_hour">Per Hour</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-outline-gold" data-bs-dismiss="modal" style="padding:8px 18px;font-size:13px;">Cancel</button>
                    <button type="submit" class="btn-gold" id="listingSubmitBtn" style="padding:8px 18px;font-size:13px;">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const listingModal = new bootstrap.Modal(document.getElementById('listingModal'));
let editingListingId = null;

document.getElementById('listingForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const isEdit = !!editingListingId;
    const url = isEdit ? `/vendor/listings/${editingListingId}` : '{{ route("vendor.listings.store") }}';
    if (isEdit) formData.append('_method', 'PUT');

    fetch(url, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
        body: formData
    })
    .then(res => res.json())
    .then(data => { if (data.success) location.reload(); });
});

document.querySelectorAll('.edit-listing').forEach(btn => {
    btn.addEventListener('click', function() {
        editingListingId = this.dataset.id;
        document.getElementById('listingModalTitle').textContent = 'Edit Service';
        document.getElementById('listing_id').value = this.dataset.id;
        document.getElementById('service_category_id').value = this.dataset.category_id;
        document.getElementById('title').value = this.dataset.title;
        document.getElementById('description').value = this.dataset.description;
        document.getElementById('price').value = this.dataset.price;
        document.getElementById('price_unit').value = this.dataset.price_unit;
        document.getElementById('listingSubmitBtn').textContent = 'Update';
        listingModal.show();
    });
});

document.querySelectorAll('.delete-listing').forEach(btn => {
    btn.addEventListener('click', function() {
        if (!confirm('Delete this listing?')) return;
        fetch(`/vendor/listings/${this.dataset.id}`, {
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

document.getElementById('listingModal').addEventListener('hidden.bs.modal', function() {
    editingListingId = null;
    document.getElementById('listingForm').reset();
    document.getElementById('listingModalTitle').textContent = 'Add Service';
    document.getElementById('listingSubmitBtn').textContent = 'Save';
});
</script>
@endpush
