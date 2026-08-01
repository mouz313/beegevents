@extends('vendor.layouts.master')

@section('title', 'Manage Menu')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 style="margin:0;color:var(--charcoal);font-weight:700;">Food Menu</h2>
            <div style="font-size:13px;color:var(--text-muted);">Add menu categories and items for customers to view.</div>
        </div>
        <button class="btn-gold" data-bs-toggle="modal" data-bs-target="#categoryModal">
            <i class="ti ti-plus"></i> Add Category
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="border-radius:10px;font-size:13px;">{{ session('success') }}</div>
    @endif

    <div class="row g-4">
        <div class="col-md-4">
            <div class="profile-card">
                <div class="card-body-custom" style="padding:20px;">
                    <div class="form-label-custom mb-2">Categories</div>
                    @forelse($categories as $category)
                        <div class="menu-cat-item {{ $loop->first ? 'active' : '' }}" data-category-id="{{ $category->id }}" style="cursor:pointer;">
                            <div class="d-flex justify-content-between align-items-center">
                                <div style="flex:1;">
                                    <strong style="font-size:14px;color:var(--charcoal);">{{ $category->name }}</strong>
                                    <div style="font-size:11px;color:var(--text-muted);">{{ $category->menuItems->count() }} item(s)</div>
                                </div>
                                <div class="d-flex gap-1">
                                    <button class="btn btn-sm btn-outline-secondary btn-edit-cat" data-id="{{ $category->id }}" data-name="{{ $category->name }}" title="Edit" style="padding:2px 8px;font-size:11px;">
                                        <i class="ti ti-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger btn-delete-cat" data-id="{{ $category->id }}" title="Delete" style="padding:2px 8px;font-size:11px;">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state" style="padding:24px 12px;">
                            <i class="ti ti-mood-empty"></i>
                            <p class="text-muted" style="font-size:13px;margin:8px 0 0;">No categories yet. Add one to start building your menu.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="profile-card">
                <div class="card-header-custom" style="padding:16px 20px;">
                    <div style="width:36px;height:36px;border-radius:8px;background:var(--gold);display:flex;align-items:center;justify-content:center;font-size:18px;color:var(--charcoal);flex-shrink:0;">
                        <i class="ti ti-cookie"></i>
                    </div>
                    <h4 style="font-size:15px;"><span id="currentCategoryName">Menu Items</span></h4>
                    <button class="btn-gold" id="addItemBtn" style="padding:8px 16px;font-size:12px;margin-left:auto;" data-bs-toggle="modal" data-bs-target="#itemModal">
                        <i class="ti ti-plus"></i> Add Item
                    </button>
                </div>
                <div class="card-body-custom" style="padding:20px;">
                    <div id="itemsArea">
                        @php $first = $categories->first(); @endphp
                        @if($first)
                            @include('vendor.menu.items', ['category' => $first])
                        @else
                            <div class="empty-state" style="padding:40px 16px;">
                                <i class="ti ti-knife"></i>
                                <h5 style="margin-top:12px;">No menu yet</h5>
                                <p class="text-muted" style="font-size:13px;">Create a category first, then add items to it.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Category Modal --}}
<div class="modal fade" id="categoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:14px;border:none;">
            <form id="categoryForm">
                <div class="modal-header" style="border:none;padding:20px 24px 0;">
                    <h5 style="font-weight:700;" id="categoryModalTitle">Add Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding:20px 24px;">
                    <input type="hidden" id="category_id">
                    <label class="form-label" style="font-size:12px;font-weight:600;">Category Name</label>
                    <input type="text" class="form-control" id="category_name" name="name" required placeholder="e.g. Continental, Thai, Chinese, Salad, Sweets">
                </div>
                <div class="modal-footer" style="border:none;padding:0 24px 24px;">
                    <button type="button" class="btn-outline-gold" data-bs-dismiss="modal" style="padding:8px 18px;font-size:13px;">Cancel</button>
                    <button type="submit" class="btn-gold" style="padding:8px 18px;font-size:13px;">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Item Modal --}}
<div class="modal fade" id="itemModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:14px;border:none;">
            <form id="itemForm">
                <div class="modal-header" style="border:none;padding:20px 24px 0;">
                    <h5 style="font-weight:700;" id="itemModalTitle">Add Menu Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding:20px 24px;">
                    <input type="hidden" id="item_id">
                    <input type="hidden" id="item_category_id" name="menu_category_id">
                    <div class="mb-3">
                        <label class="form-label" style="font-size:12px;font-weight:600;">Item Name</label>
                        <input type="text" class="form-control" id="item_name" name="name" required placeholder="e.g. Chicken Biryani">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-size:12px;font-weight:600;">Price (PKR)</label>
                        <input type="number" step="0.01" min="0" class="form-control" id="item_price" name="price" placeholder="Optional">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-size:12px;font-weight:600;">Description</label>
                        <textarea class="form-control" id="item_description" name="description" rows="2" placeholder="Optional"></textarea>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="item_available" name="is_available" value="1" checked>
                        <label class="form-check-label" for="item_available" style="font-size:13px;">Available</label>
                    </div>
                </div>
                <div class="modal-footer" style="border:none;padding:0 24px 24px;">
                    <button type="button" class="btn-outline-gold" data-bs-dismiss="modal" style="padding:8px 18px;font-size:13px;">Cancel</button>
                    <button type="submit" class="btn-gold" style="padding:8px 18px;font-size:13px;">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.menu-cat-item {
    padding: 12px 14px;
    border: 1px solid var(--border);
    border-radius: 10px;
    margin-bottom: 8px;
    transition: all 0.15s;
}
.menu-cat-item.active {
    border-color: var(--gold);
    background: var(--light-honey);
}
.menu-cat-item:hover {
    border-color: var(--gold);
}
</style>
@endpush

@push('scripts')
<script>
const categoryModal = new bootstrap.Modal(document.getElementById('categoryModal'));
const itemModal = new bootstrap.Modal(document.getElementById('itemModal'));
let activeCategoryId = {{ $categories->first()->id ?? 'null' }};

// ---- Category selection ----
function selectCategory(id) {
    activeCategoryId = id;
    document.querySelectorAll('.menu-cat-item').forEach(el => el.classList.remove('active'));
    const el = document.querySelector(`.menu-cat-item[data-category-id="${id}"]`);
    if (el) el.classList.add('active');

    fetch('{{ route("vendor.menu.items.partial") }}', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ category_id: id })
    })
    .then(res => res.text())
    .then(html => {
        document.getElementById('itemsArea').innerHTML = html;
        bindItemActions();
        const name = document.querySelector(`.menu-cat-item[data-category-id="${id}"] strong`)?.textContent;
        document.getElementById('currentCategoryName').textContent = name || 'Menu Items';
    });
}

document.querySelectorAll('.menu-cat-item').forEach(el => {
    el.addEventListener('click', (e) => {
        if (e.target.closest('button')) return;
        selectCategory(el.dataset.categoryId);
    });
});

// ---- Category add/edit ----
document.getElementById('categoryForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const id = document.getElementById('category_id').value;
    const url = id ? `{{ url('vendor/menu/categories') }}/${id}` : '{{ route("vendor.menu.categories.store") }}';
    const formData = new FormData(this);
    if (id) formData.append('_method', 'PUT');

    fetch(url, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
        body: formData
    })
    .then(res => res.json())
    .then(data => { if (data.success) location.reload(); });
});

document.querySelectorAll('.btn-edit-cat').forEach(btn => {
    btn.addEventListener('click', (e) => {
        e.stopPropagation();
        document.getElementById('categoryModalTitle').textContent = 'Edit Category';
        document.getElementById('category_id').value = btn.dataset.id;
        document.getElementById('category_name').value = btn.dataset.name;
        categoryModal.show();
    });
});

document.querySelectorAll('.btn-delete-cat').forEach(btn => {
    btn.addEventListener('click', (e) => {
        e.stopPropagation();
        if (!confirm('Delete this category and all its items?')) return;
        fetch(`{{ url('vendor/menu/categories') }}/${btn.dataset.id}`, {
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

// ---- Item add/edit ----
document.getElementById('addItemBtn')?.addEventListener('click', function() {
    if (!activeCategoryId) return;
    document.getElementById('itemModalTitle').textContent = 'Add Menu Item';
    document.getElementById('item_id').value = '';
    document.getElementById('item_category_id').value = activeCategoryId;
    document.getElementById('itemForm').reset();
    document.getElementById('item_available').checked = true;
});

document.getElementById('itemForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const id = document.getElementById('item_id').value;
    const url = id ? `{{ url('vendor/menu/items') }}/${id}` : '{{ route("vendor.menu.items.store") }}';
    const formData = new FormData(this);
    if (id) formData.append('_method', 'PUT');

    fetch(url, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
        body: formData
    })
    .then(res => res.json())
    .then(data => { if (data.success) location.reload(); });
});

function bindItemActions() {
    document.querySelectorAll('.btn-edit-item').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('itemModalTitle').textContent = 'Edit Menu Item';
            document.getElementById('item_id').value = btn.dataset.id;
            document.getElementById('item_category_id').value = btn.dataset.categoryId;
            document.getElementById('item_name').value = btn.dataset.name;
            document.getElementById('item_price').value = btn.dataset.price || '';
            document.getElementById('item_description').value = btn.dataset.description || '';
            document.getElementById('item_available').checked = btn.dataset.available === '1';
            itemModal.show();
        });
    });

    document.querySelectorAll('.btn-delete-item').forEach(btn => {
        btn.addEventListener('click', function() {
            if (!confirm('Delete this item?')) return;
            fetch(`{{ url('vendor/menu/items') }}/${btn.dataset.id}`, {
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
}

document.getElementById('categoryModal').addEventListener('hidden.bs.modal', function() {
    document.getElementById('categoryForm').reset();
    document.getElementById('category_id').value = '';
    document.getElementById('categoryModalTitle').textContent = 'Add Category';
});

bindItemActions();
</script>
@endpush
