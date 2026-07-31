@extends('admin.layouts.master')

@section('title', $blogPost ? 'Edit Post' : 'New Post')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 style="font-size:20px;font-weight:600;">
        <i class="ti ti-news"></i> {{ $blogPost ? 'Edit Post' : 'New Post' }}
    </h2>
    <a href="{{ route('admin.blog.index') }}" class="btn btn-ghost"><i class="ti ti-arrow-left"></i> Back</a>
</div>

<div class="admin-card">
    <div class="card-body">
        <form method="POST" action="{{ $blogPost ? route('admin.blog.update', $blogPost) : route('admin.blog.store') }}">
            @csrf
            @if($blogPost) @method('PUT') @endif

            <div class="mb-3">
                <label class="form-label-admin">Title</label>
                <input type="text" name="title" class="form-control-admin w-100" value="{{ old('title', $blogPost->title ?? '') }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label-admin">Excerpt</label>
                <textarea name="excerpt" class="form-control-admin w-100" rows="2">{{ old('excerpt', $blogPost->excerpt ?? '') }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label-admin">Content</label>
                <textarea name="content" class="form-control-admin w-100" rows="12" required>{{ old('content', $blogPost->content ?? '') }}</textarea>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="form-label-admin">Featured Image URL</label>
                    <input type="url" name="featured_image" class="form-control-admin w-100" value="{{ old('featured_image', $blogPost->featured_image ?? '') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label-admin">Author</label>
                    <input type="text" name="author" class="form-control-admin w-100" value="{{ old('author', $blogPost->author ?? 'BeeG Events') }}">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <div class="form-check">
                        <input type="checkbox" name="is_published" class="form-check-input" id="isPublished" value="1" {{ old('is_published', $blogPost->is_published ?? false) ? 'checked' : '' }}>
                        <label class="form-check-label" for="isPublished">Published</label>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-gold">
                <i class="ti ti-device-floppy"></i> {{ $blogPost ? 'Update' : 'Create' }} Post
            </button>
        </form>
    </div>
</div>
@endsection
