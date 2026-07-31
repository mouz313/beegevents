@extends('layouts.app')

@section('title', 'Blog - BeeG Events')

@section('content')
<div class="container py-5">
    <div class="text-center mb-5">
        <h1 style="color:var(--charcoal);font-weight:800;font-size:2rem;">BeeG <span style="color:var(--gold);">Blog</span></h1>
        <p style="color:var(--text-muted);max-width:600px;margin:8px auto 0;">Tips, guides, and inspiration for planning your perfect event in Pakistan.</p>
    </div>

    <div class="row g-4">
        @forelse($posts as $post)
            <div class="col-md-6 col-lg-4">
                <a href="{{ route('blog.show', $post) }}" style="text-decoration:none;color:inherit;">
                    <div style="border:1px solid var(--border);border-radius:16px;overflow:hidden;background:white;transition:all 0.2s;height:100%;" onmouseover="this.style.boxShadow='0 8px 30px rgba(43,38,32,0.1)';this.style.transform='translateY(-2px)'" onmouseout="this.style.boxShadow='none';this.style.transform='none'">
                        @if($post->featured_image)
                            <img src="{{ $post->featured_image }}" alt="{{ $post->title }}" style="width:100%;height:200px;object-fit:cover;">
                        @else
                            <div style="width:100%;height:200px;background:var(--cream);display:flex;align-items:center;justify-content:center;">
                                <i class="ti ti-news" style="font-size:48px;color:var(--gold);"></i>
                            </div>
                        @endif
                        <div style="padding:20px;">
                            <span style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;">
                                {{ $post->published_at?->format('M d, Y') }} · {{ $post->author }}
                            </span>
                            <h5 style="margin:8px 0 6px;color:var(--charcoal);font-weight:700;font-size:16px;">{{ $post->title }}</h5>
                            <p style="font-size:13px;color:var(--text-muted);margin:0;line-height:1.5;">{{ $post->excerpt }}</p>
                        </div>
                    </div>
                </a>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <i class="ti ti-news-off" style="font-size:48px;color:var(--border);"></i>
                <p style="color:var(--text-muted);margin-top:12px;">No blog posts yet. Check back soon!</p>
            </div>
        @endforelse
    </div>

    @if(method_exists($posts, 'links'))
        <div class="mt-4">{{ $posts->links() }}</div>
    @endif
</div>
@endsection
