@php
    $selectedIds = $set->items->pluck('id')->all();
    $grouped = $items->groupBy(fn ($i) => $i->menuCategory->name ?? 'General');
@endphp
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <strong style="font-size:16px;color:var(--charcoal);">{{ $set->name }}</strong>
        <div style="font-size:12px;color:var(--text-muted);">
            {{ $set->items->count() }} item(s)
            @if($set->is_active)
                <span class="badge bg-success" style="font-size:10px;margin-left:6px;">Active</span>
            @else
                <span class="badge bg-secondary" style="font-size:10px;margin-left:6px;">Inactive</span>
            @endif
        </div>
        @if($set->description)
            <div style="font-size:12px;color:var(--text-muted);margin-top:2px;">{{ $set->description }}</div>
        @endif
    </div>
    <button class="btn btn-sm btn-outline-gold btn-edit-set" style="padding:4px 10px;font-size:11px;"
        data-id="{{ $set->id }}" data-name="{{ $set->name }}" data-description="{{ $set->description }}"
        data-active="{{ $set->is_active ? '1' : '0' }}" data-item-ids="{{ $set->items->pluck('id')->implode(',') }}">
        <i class="ti ti-pencil"></i> Edit Set
    </button>
</div>

@if($set->is_active)
    <div class="table-responsive">
        <table class="table vendor-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Category</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                @forelse($set->items as $item)
                    <tr>
                        <td><strong>{{ $item->name }}</strong></td>
                        <td>{{ $item->menuCategory->name ?? 'General' }}</td>
                        <td>{{ $item->price ? 'PKR ' . number_format($item->price) : '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="text-muted" style="font-size:13px;">No items in this set yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@else
    <div class="empty-state" style="padding:24px 16px;">
        <i class="ti ti-eye-off"></i>
        <p class="text-muted" style="font-size:13px;margin:8px 0 0;">This set is inactive and hidden from customers.</p>
    </div>
@endif
