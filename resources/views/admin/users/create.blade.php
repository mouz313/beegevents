@extends('admin.layouts.master')

@section('title', 'Create User')

@section('content')
<div class="admin-card" style="max-width:600px;">
    <div class="card-header">
        <h5><i class="ti ti-user-plus"></i> Create User</h5>
        <a href="{{ route('admin.users.index') }}" class="btn btn-ghost btn-sm">Back</a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label" style="font-size:12px;font-weight:600;">Name</label>
                <input type="text" class="form-control-admin w-100" name="name" value="{{ old('name') }}" required>
                @error('name') <div class="text-danger mt-1" style="font-size:12px;">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
                <label class="form-label" style="font-size:12px;font-weight:600;">Email</label>
                <input type="email" class="form-control-admin w-100" name="email" value="{{ old('email') }}" required>
                @error('email') <div class="text-danger mt-1" style="font-size:12px;">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
                <label class="form-label" style="font-size:12px;font-weight:600;">Password</label>
                <input type="password" class="form-control-admin w-100" name="password" required>
                @error('password') <div class="text-danger mt-1" style="font-size:12px;">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
                <label class="form-label" style="font-size:12px;font-weight:600;">Role</label>
                <select class="form-control-admin w-100" name="role" required>
                    <option value="customer" {{ old('role') == 'customer' ? 'selected' : '' }}>Customer</option>
                    <option value="vendor" {{ old('role') == 'vendor' ? 'selected' : '' }}>Vendor</option>
                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label" style="font-size:12px;font-weight:600;">Phone</label>
                <input type="text" class="form-control-admin w-100" name="phone" value="{{ old('phone') }}">
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-gold">Create User</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-ghost">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
