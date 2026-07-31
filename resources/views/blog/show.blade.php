@extends('layouts.app')

@section('title', $blogPost->title . ' - BeeG Events')

@section('content')
<div class="container py-5">
    <div style="max-width:800px;margin:0 auto;">
        <a href="{{ route('blog.index') }}" style="display:inline-flex;align-items:center;gap:6px;color:var(--text-muted);text-decoration:none;font-size:14px;margin-bottom:16px;">
            <i class="ti ti-arrow-left"></i> Back to Blog
        </a>
        <h1 style="color:var(--charcoal);font-weight:800;font-size:28px;line-height:1.3;">{{ $blogPost->title }}</h1>
        <div style="display:flex;gap:16px;font-size:13px;color:var(--text-muted);margin:8px 0 24px;">
            <span><i class="ti ti-calendar"></i> {{ $blogPost->published_at?->format('F d, Y') }}</span>
            <span><i class="ti ti-user"></i> {{ $blogPost->author }}</span>
        </div>

        @if($blogPost->featured_image)
            <img src="{{ $blogPost->featured_image }}" alt="{{ $blogPost->title }}" style="width:100%;max-height:400px;object-fit:cover;border-radius:16px;margin-bottom:24px;">
        @endif

        <div style="font-size:15px;line-height:1.8;color:var(--charcoal);">
            {!! nl2br(e($blogPost->content)) !!}
        </div>

        @if($related->count() > 0)
            <hr style="border-color:var(--border);margin:40px 0 24px;">
            <h4 style="color:var(--charcoal);font-weight:700;margin-bottom:16px;">Related Posts</h4>
            <div class="row g-3">
                @foreach($related as $post)
                    <div class="col-md-4">
                        <a href="{{ route('blog.show', $post) }}" style="text-decoration:none;color:inherit;">
                            <div style="border:1px solid var(--border);border-radius:12px;padding:16px;background:white;height:100%;">
                                <span style="font-size:11px;color:var(--text-muted);">{{ $post->published_at?->format('M d, Y') }}</span>
                                <h6 style="margin:4px 0 0;font-size:14px;font-weight:600;color:var(--charcoal);">{{ $post->title }}</h6>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
