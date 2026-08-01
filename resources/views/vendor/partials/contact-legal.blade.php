@php
    $values = $values ?? [];
@endphp
<div class="spec-section-title"><i class="ti ti-address-book"></i> Contact &amp; Legal</div>
<div class="row g-3">
    @foreach(config('vendor-specs.common_fields', []) as $field)
        @php
            $key = $field['key'];
            $value = $values[$key] ?? null;
            $old = old($key);
            if ($old !== null) {
                $value = $old;
            }
            $required = !empty($field['required']);
        @endphp
        @if(($field['type'] ?? 'text') === 'file')
            <div class="col-md-6">
                <label class="form-label-custom">{{ $field['label'] }} @if($required)<span style="color:var(--red);">*</span>@endif</label>
                @if(!empty($values['legal_doc_path']))
                    <div class="mb-1">
                        <a href="{{ asset('storage/' . $values['legal_doc_path']) }}" target="_blank" style="font-size:12px;color:var(--gold-dark);">View uploaded document</a>
                    </div>
                @endif
                <input type="file" name="{{ $key }}" class="form-control input-custom" accept=".pdf,.jpg,.jpeg,.png">
                @if(!empty($field['help']))<div style="font-size:11px;color:var(--text-muted);margin-top:4px;">{{ $field['help'] }}</div>@endif
                @error($key)<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>
        @else
            <div class="col-md-6">
                <label class="form-label-custom">{{ $field['label'] }} @if($required)<span style="color:var(--red);">*</span>@endif</label>
                <input type="text" name="{{ $key }}" value="{{ $value }}" class="form-control input-custom">
                @error($key)<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>
        @endif
    @endforeach
</div>
