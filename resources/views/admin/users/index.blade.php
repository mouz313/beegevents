@extends('admin.layouts.master')

@section('title', 'Users')

@section('content')
<div class="admin-card">
    <div class="card-header">
        <h5><i class="ti ti-users"></i> All Users</h5>
        <a href="{{ route('admin.users.create') }}" class="btn btn-gold btn-sm">
            <i class="ti ti-plus"></i> Add User
        </a>
    </div>
    <div class="card-body p-0">
        @if($users->count() > 0)
            <table class="table-admin">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Phone</th>
                        <th>Joined</th>
                        <th style="width:120px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td><strong>{{ $user->name }}</strong></td>
                            <td><span class="text-muted">{{ $user->email }}</span></td>
                            <td>
                                <span class="status-badge status-{{ $user->role == 'admin' ? 'confirmed' : ($user->role == 'vendor' ? 'pending' : 'requested') }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td>{{ $user->phone ?? '—' }}</td>
                            <td>{{ $user->created_at->format('M d, Y') }}</td>
                            <td>
                                <a href="{{ route('admin.users.show', $user) }}" class="btn btn-ghost btn-sm">
                                    <i class="ti ti-eye"></i>
                                </a>
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-ghost btn-sm">
                                    <i class="ti ti-edit"></i>
                                </a>
                                <button class="btn btn-ghost btn-sm delete-user" data-id="{{ $user->id }}">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="p-3">{{ $users->links() }}</div>
        @else
            <div class="text-center py-5">
                <i class="ti ti-users-off" style="font-size:36px;color:var(--text-muted);"></i>
                <p class="text-muted mt-2">No users found.</p>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.delete-user').forEach(btn => {
    btn.addEventListener('click', function() {
        if (!confirm('Delete this user?')) return;
        fetch(this.dataset.id, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: new URLSearchParams({ _method: 'DELETE' })
        })
        .then(res => res.json())
        .then(data => { if (data.success) location.reload(); });
    });
});
</script>
@endpush
