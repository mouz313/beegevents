<div class="unit-card" data-unit-id="{{ $unit->id }}">
    <div class="d-flex align-items-start gap-3">
        <div class="unit-icon">
            <i class="ti ti-building"></i>
        </div>
        <div class="flex-grow-1 min-w-0">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <h5 class="unit-name mb-0">{{ $unit->unit_name }}</h5>
                <span class="unit-status avail" id="status-{{ $unit->id }}">Available</span>
            </div>
            <div class="unit-detail mt-1">
                <span><i class="ti ti-users"></i> {{ $unit->min_capacity }}-{{ $unit->max_capacity }} guests</span>
                @if($unit->floor)
                    <span><i class="ti ti-layers"></i> {{ $unit->floor->floor_label }}</span>
                @endif
                <span><i class="ti ti-palette"></i> {{ ucfirst($unit->decor_type) }} decor</span>
                @if($unit->menu_summary)
                    <span><i class="ti ti-menu-2"></i> {{ $unit->menu_summary }}</span>
                @endif
            </div>
        </div>
        <div class="text-end flex-shrink-0">
            <div class="unit-price">PKR {{ number_format($unit->base_price) }}</div>
            @auth
                @if(auth()->user()->role == 'customer')
                    <button class="btn-gold btn-sm add-to-cart mt-2" data-type="hall_unit" data-id="{{ $unit->id }}">
                        <i class="ti ti-shopping-cart"></i> Add
                    </button>
                @endif
            @endauth
        </div>
    </div>
</div>
