@php
    $def = config('vendor-specs.types.' . ($typeKey ?? ''));
    if (!$def) {
        return;
    }
    $values = $values ?? [];
    $selected = $selected ?? $typeKey;
    $show = $selected === $typeKey;
@endphp
@once
<style>
.spec-section-title {
    font-size: 13px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--gold-dark);
    margin: 24px 0 12px;
    padding-bottom: 6px;
    border-bottom: 2px solid var(--cream-dark);
    display: flex;
    align-items: center;
    gap: 8px;
}
.spec-chip-input { display: none; }
.spec-chip-label {
    display: inline-flex;
    align-items: center;
    padding: 7px 14px;
    border: 2px solid var(--border);
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    color: var(--text-muted);
    cursor: pointer;
    transition: all 0.15s;
    user-select: none;
    background: white;
}
.spec-chip-label:has(.spec-chip-input:checked) {
    background: var(--light-honey);
    border-color: var(--gold);
    color: var(--gold-dark);
}
.spec-toggle-input { display: none; }
.spec-toggle-switch {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
    user-select: none;
    padding: 10px 0;
}
.spec-toggle-track {
    width: 42px;
    height: 24px;
    border-radius: 24px;
    background: var(--border);
    border: 2px solid var(--border);
    position: relative;
    transition: background 0.2s, border-color 0.2s;
    flex-shrink: 0;
}
.spec-toggle-track::after {
    content: '';
    position: absolute;
    top: 2px;
    left: 2px;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    background: #fff;
    transition: transform 0.2s;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
}
.spec-toggle-input:checked + .spec-toggle-track {
    background: var(--gold);
    border-color: var(--gold);
}
.spec-toggle-input:checked + .spec-toggle-track::after {
    transform: translateX(18px);
}
.spec-toggle-text {
    font-size: 13px;
    font-weight: 600;
    color: var(--charcoal);
}
.spec-toggle-row {
    display: flex;
    flex-wrap: wrap;
    gap: 4px 28px;
    padding: 6px 0;
}
</style>
@endonce
<div class="spec-group" data-spec-group="{{ $typeKey }}" style="{{ $show ? '' : 'display:none;' }}">
    <div class="spec-section-title"><i class="{{ $def['icon'] ?? 'ti ti-settings' }}"></i> {{ $def['label'] }} Specifications</div>
    <div class="row g-3">
        @php
            $checkboxFields = array_filter($def['fields'], function ($f) {
                return ($f['type'] ?? 'text') === 'checkbox';
            });
        @endphp
        @if($checkboxFields)
            <div class="col-12">
                <div class="spec-toggle-row">
                    @foreach($checkboxFields as $field)
                        @php
                            $key = $field['key'];
                            $name = !empty($field['flat']) ? $key : 'specs[' . $key . ']';
                            $value = $values[$key] ?? null;
                            $old = old($name);
                            if ($old !== null) {
                                $value = $old;
                            }
                        @endphp
                        <label class="spec-toggle-switch">
                            <input type="checkbox" name="{{ $name }}" value="1" {{ $value ? 'checked' : '' }} class="spec-toggle-input">
                            <span class="spec-toggle-track"></span>
                            <span class="spec-toggle-text">{{ $field['label'] }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        @endif
        @foreach($def['fields'] as $field)
            @php
                $key = $field['key'];
                $name = !empty($field['flat']) ? $key : 'specs[' . $key . ']';
                $value = $values[$key] ?? null;
                $old = old($name);
                if ($old !== null) {
                    $value = $old;
                }
                $type = $field['type'] ?? 'text';
                $required = !empty($field['required']);
            @endphp

            @unless($type === 'checkbox')
                @if($type === 'multi_select')
                <div class="col-12">
                    <label class="form-label-custom">{{ $field['label'] }}</label>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($field['options'] as $opt)
                            <label style="margin:0;">
                                <input type="checkbox" name="{{ $name }}[]" value="{{ $opt }}" {{ in_array($opt, (array) $value) ? 'checked' : '' }} class="spec-chip-input">
                                <span class="spec-chip-label">{{ $opt }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error($name)<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>
            @elseif($type === 'select')
                <div class="col-md-6">
                    <label class="form-label-custom">{{ $field['label'] }} @if($required)<span style="color:var(--red);">*</span>@endif</label>
                    <select name="{{ $name }}" class="form-select input-custom">
                        <option value="">Select&hellip;</option>
                        @foreach($field['options'] as $opt)
                            <option value="{{ $opt }}" {{ $value == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                        @endforeach
                    </select>
                    @error($name)<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>
            @elseif($type === 'textarea')
                <div class="col-12">
                    <label class="form-label-custom">{{ $field['label'] }} @if($required)<span style="color:var(--red);">*</span>@endif</label>
                    <textarea name="{{ $name }}" rows="3" class="form-control input-custom">{{ $value }}</textarea>
                    @error($name)<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>
            @else
                <div class="col-md-6">
                    <label class="form-label-custom">{{ $field['label'] }} @if($required)<span style="color:var(--red);">*</span>@endif</label>
                    <div class="d-flex align-items-center gap-2">
                        <input type="{{ $type === 'number' ? 'number' : 'text' }}"
                               name="{{ $name }}"
                               value="{{ $value }}"
                               class="form-control input-custom"
                               @if($type === 'number') step="any" min="{{ $field['min'] ?? 0 }}" @endif
                               placeholder="{{ $field['placeholder'] ?? '' }}">
                        @if(!empty($field['suffix']))
                            <span style="font-size:12px;font-weight:600;color:var(--text-muted);white-space:nowrap;">{{ $field['suffix'] }}</span>
                        @endif
                    </div>
                    @if(!empty($field['help']))<div style="font-size:11px;color:var(--text-muted);margin-top:4px;">{{ $field['help'] }}</div>@endif
                    @error($name)<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>
                @endif
            @endunless
        @endforeach
    </div>
</div>
