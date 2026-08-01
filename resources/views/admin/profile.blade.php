@extends('admin.layouts.master')

@section('title', 'Profile Settings')

@section('content')
@php $user = auth()->user(); @endphp

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 style="font-size:20px;font-weight:600;margin:0;">
            <i class="ti ti-user"></i> Profile Settings
        </h2>
        <span style="font-size:13px;color:var(--text-muted);">Update your photo, name, contact number and password.</span>
    </div>
</div>

@if($errors->any())
    <div class="admin-card mb-3" style="border-color:var(--red);">
        <div class="card-body">
            <ul style="margin:0;color:var(--red);font-size:13px;">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    </div>
@endif

<div class="row">
    <div class="col-lg-4">
        <div class="admin-card">
            <div class="card-header"><h5><i class="ti ti-photo"></i> Profile Photo</h5></div>
            <div class="card-body" style="text-align:center;">
                <form method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data" id="avatarForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="name" value="{{ $user->name }}">
                    <input type="hidden" name="email" value="{{ $user->email }}">
                    <input type="hidden" name="phone" value="{{ $user->phone }}">
                    <label for="avatarInput" style="cursor:pointer;display:block;">
                        <div style="width:110px;height:110px;border-radius:50%;overflow:hidden;margin:0 auto 12px;background:var(--cream);border:2px solid var(--gold);display:flex;align-items:center;justify-content:center;">
                            @if($user->avatar_path)
                                <img src="{{ asset('storage/'.$user->avatar_path) }}" alt="Avatar" id="avatarPreview" style="width:100%;height:100%;object-fit:cover;">
                            @else
                                <span id="avatarPreview" style="font-size:40px;font-weight:700;color:var(--gold-dark);">{{ substr($user->name, 0, 1) }}</span>
                            @endif
                        </div>
                        <div style="font-size:12px;color:var(--text-muted);"><i class="ti ti-camera"></i> Click to change photo</div>
                    </label>
                    <input type="file" name="avatar" id="avatarInput" accept="image/*" style="display:none;">
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <form method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="admin-card">
                <div class="card-header"><h5><i class="ti ti-user"></i> Account Information</h5></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control-admin w-100" name="name" value="{{ old('name', $user->name) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="text" class="form-control-admin w-100" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="+92 3xx xxxxxxx">
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control-admin w-100" name="email" value="{{ old('email', $user->email) }}" required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="admin-card mt-3">
                <div class="card-header"><h5><i class="ti ti-lock"></i> Change Password</h5></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Current Password</label>
                            <input type="password" class="form-control-admin w-100" name="current_password" placeholder="Enter current password" autocomplete="current-password">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">New Password</label>
                            <input type="password" class="form-control-admin w-100" name="password" placeholder="Min 8 characters" autocomplete="new-password">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control-admin w-100" name="password_confirmation" placeholder="Repeat new password" autocomplete="new-password">
                        </div>
                    </div>
                    <div style="font-size:12px;color:var(--text-muted);">Leave password fields empty to keep your current password.</div>
                </div>
            </div>

            <div style="margin-top:16px;text-align:right;">
                <button type="submit" class="btn btn-gold"><i class="ti ti-device-floppy"></i> Save Profile</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
const avatarInput = document.getElementById('avatarInput');
if (avatarInput) {
    avatarInput.addEventListener('change', function () {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = e => {
                const prev = document.getElementById('avatarPreview');
                if (prev.tagName === 'IMG') {
                    prev.src = e.target.result;
                } else {
                    const img = document.createElement('img');
                    img.id = 'avatarPreview';
                    img.src = e.target.result;
                    img.style.cssText = 'width:100%;height:100%;object-fit:cover;';
                    prev.replaceWith(img);
                }
            };
            reader.readAsDataURL(this.files[0]);
            document.getElementById('avatarForm').submit();
        }
    });
}
</script>
@endpush
