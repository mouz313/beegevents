@php
    $selectedType = $selectedType ?? old('vendor_type') ?? 'hall';
    $markChecked = $markChecked ?? false;
@endphp
<div class="type-toggle-group" id="{{ $groupId ?? 'vendorTypeToggle' }}">
    @foreach(config('vendor-specs.types', []) as $typeKey => $typeDef)
        <label class="type-toggle-label">
            <input type="radio" class="type-toggle-input" name="vendor_type" value="{{ $typeKey }}"
                   {{ $markChecked && $selectedType === $typeKey ? 'checked' : '' }} required>
            <i class="{{ $typeDef['icon'] ?? 'ti ti-settings' }}"></i>
            <span>{{ $typeDef['label'] }}</span>
        </label>
    @endforeach
</div>
