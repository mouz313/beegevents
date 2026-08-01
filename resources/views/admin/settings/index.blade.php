@extends('admin.layouts.master')

@section('title', 'Settings')

@section('content')
@php
    $groupIcons = [
        'general' => 'ti ti-settings',
        'social' => 'ti ti-share',
        'business' => 'ti ti-building-bank',
        'status' => 'ti ti-toggle-left',
        'slider' => 'ti ti-photo',
    ];
@endphp

<div class="d-flex justify-content-between align-items-center mb-4" style="flex-wrap:wrap;gap:8px;">
    <div>
        <h2 class="settings-page-title">
            <i class="ti ti-settings"></i> Settings
        </h2>
        <div class="settings-page-subtitle">Manage site settings, contact details, social links and business rules.</div>
    </div>
    <button class="btn btn-gold btn-sm" data-bs-toggle="modal" data-bs-target="#addSettingModal">
        <i class="ti ti-plus"></i> Add Setting
    </button>
</div>

@if($groups->count())
    <div class="admin-card settings-tab-wrap">
        <div class="card-header" style="padding-bottom:0;border-bottom:0;">
            <ul class="nav nav-tabs settings-tabs w-100" role="tablist">
                @foreach($groups as $groupName => $groupSettings)
                    @php $gid = \Str::slug($groupName); @endphp
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $loop->first ? 'active' : '' }}" data-bs-toggle="tab"
                                data-bs-target="#group-{{ $gid }}" type="button" role="tab">
                            <i class="{{ $groupIcons[$groupName] ?? 'ti ti-adjustments' }}"></i>
                            {{ ucfirst($groupName) }}
                            <span class="badge bg-secondary ms-1" style="font-size:10px;">{{ $groupSettings->count() }}</span>
                        </button>
                    </li>
                @endforeach
            </ul>
        </div>
        <div class="card-body p-0">
            <div class="tab-content">
                @foreach($groups as $groupName => $groupSettings)
                    @php $gid = \Str::slug($groupName); @endphp
                    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="group-{{ $gid }}" role="tabpanel">
                        <div class="settings-savebar">
                            <span class="settings-change-count" id="changes-{{ $gid }}"></span>
                            <button class="btn btn-gold btn-sm save-group" data-group="{{ $gid }}" disabled>
                                <i class="ti ti-device-floppy"></i> Save {{ ucfirst($groupName) }} Group
                            </button>
                        </div>
                        <table class="settings-table">
                            <thead>
                                <tr>
                                    <th style="width:30%;">Setting</th>
                                    <th>Value</th>
                                    <th style="width:60px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($groupSettings as $setting)
                                    <tr>
                                        <td>
                                            <div class="setting-name">{{ $setting->label }}</div>
                                            <code class="settings-key">{{ $setting->key }}</code>
                                            @if($setting->description)
                                                <div class="settings-desc">{{ $setting->description }}</div>
                                            @endif
                                        </td>
                                        <td>
                                            @if($setting->type === 'image')
                                                <form method="POST" action="{{ route('admin.settings.update', $setting) }}" enctype="multipart/form-data" style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="label" value="{{ $setting->label }}">
                                                    <input type="hidden" name="group" value="{{ $setting->group }}">
                                                    <input type="hidden" name="type" value="image">
                                                    <input type="hidden" name="description" value="{{ $setting->description }}">
                                                    <input type="hidden" name="is_public" value="{{ $setting->is_public ? 1 : 0 }}">
                                                    <label class="avatar" style="width:44px;height:44px;border-radius:10px;overflow:hidden;cursor:pointer;border:1px solid var(--border);background:var(--cream);display:flex;align-items:center;justify-content:center;">
                                                        @if($setting->value)
                                                            <img src="{{ asset('storage/'.$setting->value) }}" alt="{{ $setting->label }}" style="width:100%;height:100%;object-fit:cover;">
                                                        @else
                                                            <i class="ti ti-photo" style="font-size:18px;color:var(--text-muted);"></i>
                                                        @endif
                                                        <input type="file" name="value" accept="image/*" style="display:none;" onchange="this.form.submit()">
                                                    </label>
                                                    <span style="font-size:11px;color:var(--text-muted);">Click image to change</span>
                                                </form>
                                            @elseif($setting->type === 'boolean')
                                                <div style="display:flex;align-items:center;gap:10px;">
                                                    <label class="switch" title="{{ $setting->value ? 'Enabled' : 'Disabled' }}">
                                                        <input type="hidden" name="settings[{{ $setting->id }}][value]" value="0">
                                                        <input type="checkbox" name="settings[{{ $setting->id }}][value]" value="1" {{ $setting->value ? 'checked' : '' }}>
                                                        <span class="slider"></span>
                                                    </label>
                                                    <span class="setting-toggle-state">{{ $setting->value ? 'Enabled' : 'Disabled' }}</span>
                                                </div>
                                            @elseif($setting->type === 'textarea')
                                                <textarea class="setting-input" rows="2" name="settings[{{ $setting->id }}][value]">{{ $setting->value }}</textarea>
                                            @else
                                                <input type="{{ $setting->type === 'number' ? 'number' : 'text' }}" class="setting-input"
                                                       name="settings[{{ $setting->id }}][value]"
                                                       value="{{ $setting->type === 'email' || $setting->type === 'url' ? e($setting->value) : $setting->value }}">
                                            @endif
                                        </td>
                                        <td>
                                            <button class="btn-icon-danger delete-setting" data-id="{{ $setting->id }}" data-label="{{ $setting->label }}" title="Delete">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@else
    <div class="admin-card">
        <div class="card-body" style="text-align:center;padding:40px;color:var(--text-muted);">
            <i class="ti ti-settings" style="font-size:40px;"></i>
            <p style="margin-top:10px;">No settings yet. Add your first setting.</p>
        </div>
    </div>
@endif

<div class="modal fade" id="addSettingModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.settings.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="ti ti-plus"></i> Add New Setting</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding:20px;">
                    <div class="mb-3">
                        <label class="form-label">Key <span class="text-danger">*</span></label>
                        <input type="text" class="form-control-admin w-100" name="key" placeholder="e.g. site_name" pattern="[a-z0-9_.]+" required>
                        <div class="form-hint">Lowercase, numbers, dots and underscores only.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Label <span class="text-danger">*</span></label>
                        <input type="text" class="form-control-admin w-100" name="label" placeholder="e.g. Site Name" required>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Group <span class="text-danger">*</span></label>
                            <input type="text" class="form-control-admin w-100" name="group" list="settingGroups" placeholder="e.g. general" required>
                            <datalist id="settingGroups">
                                @foreach($groups as $g => $items)<option value="{{ $g }}">@endforeach
                            </datalist>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Type <span class="text-danger">*</span></label>
                            <select class="form-control-admin w-100" name="type" id="newSettingType" required>
                                <option value="text">Text</option>
                                <option value="textarea">Textarea</option>
                                <option value="number">Number</option>
                                <option value="email">Email</option>
                                <option value="url">URL</option>
                                <option value="image">Image</option>
                                <option value="boolean">Yes / No</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3" id="newSettingValueWrap">
                        <label class="form-label">Value</label>
                        <input type="text" class="form-control-admin w-100" name="value" placeholder="Setting value">
                    </div>
                    <div class="mb-3" id="newSettingFileWrap" style="display:none;">
                        <label class="form-label">Value (Image)</label>
                        <input type="file" class="form-control-admin w-100" name="value" accept="image/*">
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Description</label>
                        <textarea class="form-control-admin w-100" name="description" rows="2" placeholder="What is this setting for?"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-gold">Add Setting</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function initGroup(groupKey) {
    const scope = document.getElementById('group-' + groupKey);
    if (!scope) return;
    const saveBtn = scope.querySelector('.save-group');
    const countEl = document.getElementById('changes-' + groupKey);
    const fields = Array.from(scope.querySelectorAll('input[name^="settings["], textarea[name^="settings["'));
    const original = {};
    fields.forEach(el => {
        const m = el.name.match(/^settings\[(\d+)\]\[value\]$/);
        if (!m) return;
        original[m[1]] = el.type === 'checkbox' ? (el.checked ? '1' : '0') : el.value;
    });

    const refresh = () => {
        let changed = 0;
        fields.forEach(el => {
            const m = el.name.match(/^settings\[(\d+)\]\[value\]$/);
            if (!m) return;
            const v = el.type === 'checkbox' ? (el.checked ? '1' : '0') : el.value;
            if (original[m[1]] !== v) changed++;
        });
        if (saveBtn) saveBtn.disabled = changed === 0;
        if (countEl) countEl.textContent = changed ? changed + ' unsaved change' + (changed > 1 ? 's' : '') : '';
    };

    fields.forEach(el => {
        el.addEventListener('input', refresh);
        el.addEventListener('change', refresh);
    });

    scope.querySelectorAll('input[type="checkbox"][name^="settings["]').forEach(cb => {
        cb.addEventListener('change', () => {
            const state = cb.closest('div')?.querySelector('.setting-toggle-state');
            if (state) {
                state.textContent = cb.checked ? 'Enabled' : 'Disabled';
                state.style.color = cb.checked ? 'var(--green)' : '';
            }
        });
    });

    refresh();
}

@foreach($groups as $groupName => $groupSettings)
    initGroup('{{ \Str::slug($groupName) }}');
@endforeach

document.querySelectorAll('.settings-tabs [data-bs-toggle="tab"]').forEach(btn => {
    btn.addEventListener('shown.bs.tab', () => {
        initGroup(btn.getAttribute('data-bs-target').replace('#', ''));
    });
});

document.getElementById('newSettingType').addEventListener('change', function () {
    const isImage = this.value === 'image';
    const isBool = this.value === 'boolean';
    document.getElementById('newSettingValueWrap').style.display = (isImage || isBool) ? 'none' : '';
    document.getElementById('newSettingFileWrap').style.display = isImage ? '' : 'none';
    if (isBool) {
        document.getElementById('newSettingValueWrap').innerHTML =
            '<label class="form-label">Default Value</label>' +
            '<div style="display:flex;align-items:center;gap:8px;">' +
            '<input type="checkbox" name="value" value="1" class="form-check-input" style="width:20px;height:20px;">' +
            '<span style="font-size:12px;color:var(--text-muted);">Enabled by default</span></div>';
    } else if (!isImage) {
        document.getElementById('newSettingValueWrap').innerHTML =
            '<label class="form-label">Value</label>' +
            '<input type="text" class="form-control-admin w-100" name="value" placeholder="Setting value">';
    }
});

document.querySelectorAll('.save-group').forEach(btn => {
    btn.addEventListener('click', function () {
        const groupKey = this.dataset.group;
        const scope = document.getElementById('group-' + groupKey);
        const payload = {};
        scope.querySelectorAll('input[name^="settings["], textarea[name^="settings["').forEach(el => {
            const m = el.name.match(/^settings\[(\d+)\]\[value\]$/);
            if (!m) return;
            payload[m[1]] = el.type === 'checkbox' ? (el.checked ? '1' : '0') : el.value;
        });

        fetch('{{ route('admin.settings.bulk') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ settings: payload })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showToast('success', 'Saved', (data.updated || 0) + ' setting(s) updated.');
                initGroup(groupKey);
            } else {
                showToast('error', 'Error', 'Could not save settings.');
            }
        })
        .catch(() => showToast('error', 'Error', 'Could not save settings.'));
    });
});

document.querySelectorAll('.delete-setting').forEach(btn => {
    btn.addEventListener('click', function () {
        const id = this.dataset.id;
        const label = this.dataset.label;
        if (!confirm('Delete setting "' + label + '"?')) return;

        fetch('/admin/settings/' + id, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showToast('success', 'Deleted', 'Setting "' + label + '" removed.');
                setTimeout(() => location.reload(), 500);
            }
        })
        .catch(() => showToast('error', 'Error', 'Could not delete setting.'));
    });
});
</script>
@endpush
