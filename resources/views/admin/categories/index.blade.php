@extends('admin.layouts.master')

@section('title', 'Categories')

@section('content')
<div class="admin-card">
    <div class="card-header">
        <h5><i class="ti ti-category"></i> Service Categories</h5>
        <button class="btn btn-gold btn-sm" data-bs-toggle="modal" data-bs-target="#categoryModal">
            <i class="ti ti-plus"></i> Add
        </button>
    </div>
    <div class="card-body p-0">
        <table class="table-admin">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Slug</th>
                    <th style="width:100px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categories as $cat)
                    <tr>
                        <td><strong>{{ $cat->name }}</strong></td>
                        <td><span class="text-muted">{{ $cat->slug }}</span></td>
                        <td>
                            <button class="btn btn-ghost btn-sm edit-category" data-id="{{ $cat->id }}" data-name="{{ $cat->name }}" data-slug="{{ $cat->slug }}" data-description="{{ $cat->description }}">
                                <i class="ti ti-edit"></i>
                            </button>
                            <button class="btn btn-ghost btn-sm delete-category" data-id="{{ $cat->id }}">
                                <i class="ti ti-trash"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="categoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:12px;">
            <form id="categoryForm">
                @csrf
                <div class="modal-header" style="border-bottom:1px solid var(--border);padding:16px 20px;">
                    <h5 class="modal-title" style="font-size:15px;font-weight:600;">Add Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding:20px;">
                    <div class="mb-3">
                        <label class="form-label" style="font-size:12px;font-weight:600;">Name</label>
                        <input type="text" class="form-control-admin w-100" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-size:12px;font-weight:600;">Slug</label>
                        <input type="text" class="form-control-admin w-100" name="slug" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-size:12px;font-weight:600;">Description</label>
                        <textarea class="form-control-admin w-100" name="description" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid var(--border);padding:12px 20px;">
                    <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-gold">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:12px;">
            <form id="editCategoryForm">
                @csrf @method('PUT')
                <div class="modal-header" style="border-bottom:1px solid var(--border);padding:16px 20px;">
                    <h5 class="modal-title" style="font-size:15px;font-weight:600;">Edit Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding:20px;">
                    <div class="mb-3">
                        <label class="form-label" style="font-size:12px;font-weight:600;">Name</label>
                        <input type="text" class="form-control-admin w-100" id="editName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-size:12px;font-weight:600;">Slug</label>
                        <input type="text" class="form-control-admin w-100" id="editSlug" name="slug" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-size:12px;font-weight:600;">Description</label>
                        <textarea class="form-control-admin w-100" id="editDescription" name="description" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid var(--border);padding:12px 20px;">
                    <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-gold">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let editingCategoryId = null;

document.getElementById('categoryForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    fetch('{{ route("admin.categories.store") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => { if (data.success) location.reload(); });
});

document.querySelectorAll('.edit-category').forEach(btn => {
    btn.addEventListener('click', function() {
        editingCategoryId = this.dataset.id;
        document.getElementById('editName').value = this.dataset.name;
        document.getElementById('editSlug').value = this.dataset.slug;
        document.getElementById('editDescription').value = this.dataset.description;
        new bootstrap.Modal(document.getElementById('editCategoryModal')).show();
    });
});

document.getElementById('editCategoryForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    fetch('/admin/categories/' + editingCategoryId, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => { if (data.success) location.reload(); });
});

document.querySelectorAll('.delete-category').forEach(btn => {
    btn.addEventListener('click', function() {
        if (!confirm('Delete this category?')) return;
        fetch('/admin/categories/' + this.dataset.id, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: new URLSearchParams({ _method: 'DELETE' })
        })
        .then(res => res.json())
        .then(data => { if (data.success) location.reload(); });
    });
});
</script>
@endpush
