@extends('admin.layouts.master')

@section('title', 'Edit User')

@section('content')
<div class="admin-card" style="max-width:600px;">
    <div class="card-header">
        <h5><i class="ti ti-user-edit"></i> Edit User: {{ $user->name }}</h5>
        <a href="{{ route('admin.users.index') }}" class="btn btn-ghost btn-sm">Back</a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label" style="font-size:12px;font-weight:600;">Name</label>
                <input type="text" class="form-control-admin w-100" name="name" value="{{ old('name', $user->name) }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label" style="font-size:12px;font-weight:600;">Email</label>
                <input type="email" class="form-control-admin w-100" name="email" value="{{ old('email', $user->email) }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label" style="font-size:12px;font-weight:600;">New Password <span class="text-muted">(leave blank to keep current)</span></label>
                <input type="password" class="form-control-admin w-100" name="password">
            </div>
            <div class="mb-3">
                <label class="form-label" style="font-size:12px;font-weight:600;">Role</label>
                <select class="form-control-admin w-100" name="role" required>
                    <option value="customer" {{ old('role', $user->role) == 'customer' ? 'selected' : '' }}>Customer</option>
                    <option value="vendor" {{ old('role', $user->role) == 'vendor' ? 'selected' : '' }}>Vendor</option>
                    <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label" style="font-size:12px;font-weight:600;">Phone</label>
                <input type="text" class="form-control-admin w-100" name="phone" value="{{ old('phone', $user->phone) }}">
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-gold">Update User</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-ghost">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
