@extends('admin.layouts.master')

@section('title', 'Blog Posts')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 style="font-size:20px;font-weight:600;"><i class="ti ti-news"></i> Blog Posts</h2>
    <a href="{{ route('admin.blog.create') }}" class="btn btn-gold">
        <i class="ti ti-plus"></i> New Post
    </a>
</div>

<div class="admin-card">
    <div class="card-body p-0">
        <table class="table-admin">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Status</th>
                    <th>Published</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($posts as $post)
                    <tr>
                        <td style="font-weight:600;">{{ $post->title }}</td>
                        <td>{{ $post->author }}</td>
                        <td>
                            @if($post->is_published)
                                <span class="status-badge status-confirmed">Published</span>
                            @else
                                <span class="status-badge status-pending">Draft</span>
                            @endif
                        </td>
                        <td>{{ $post->published_at?->format('M d, Y') ?: '—' }}</td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.blog.edit', $post) }}" class="btn btn-ghost btn-sm">
                                    <i class="ti ti-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.blog.destroy', $post) }}" style="display:inline;" onsubmit="return confirm('Delete this post?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-ghost btn-sm" style="color:var(--red);">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
{{ $posts->links() }}
@endsection
