@php
    $items = $category->menuItems;
@endphp
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <strong style="font-size:16px;color:var(--charcoal);">{{ $category->name }}</strong>
        <div style="font-size:12px;color:var(--text-muted);">{{ $items->count() }} item(s)</div>
    </div>
</div>
@if($items->count() > 0)
    <div class="table-responsive">
        <table class="table vendor-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th style="width:110px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                    <tr>
                        <td>
                            <strong>{{ $item->name }}</strong>
                            @if($item->description)
                                <div style="font-size:11px;color:var(--text-muted);">{{ Str::limit($item->description, 60) }}</div>
                            @endif
                        </td>
                        <td>{{ $item->price ? 'PKR ' . number_format($item->price) : '—' }}</td>
                        <td>
                            @if($item->is_available)
                                <span class="badge bg-success" style="font-size:10px;">Available</span>
                            @else
                                <span class="badge bg-secondary" style="font-size:10px;">Unavailable</span>
                            @endif
                        </td>
                        <td>
                            <button class="btn btn-sm btn-outline-gold btn-edit-item" style="padding:2px 8px;font-size:11px;"
                                data-id="{{ $item->id }}"
                                data-category-id="{{ $item->menu_category_id }}"
                                data-name="{{ $item->name }}"
                                data-price="{{ $item->price }}"
                                data-description="{{ $item->description }}"
                                data-available="{{ $item->is_available ? '1' : '0' }}">
                                <i class="ti ti-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger btn-delete-item" style="padding:2px 8px;font-size:11px;" data-id="{{ $item->id }}">
                                <i class="ti ti-trash"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <div class="empty-state" style="padding:32px 16px;">
        <i class="ti ti-knife"></i>
        <p class="text-muted" style="font-size:13px;margin:8px 0 0;">No items in this category yet.</p>
    </div>
@endif
