<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;

class BlogController extends Controller
{
    public function index()
    {
        $posts = BlogPost::where('is_published', true)
            ->orderBy('published_at', 'desc')
            ->paginate(12);
        return view('blog.index', compact('posts'));
    }

    public function show(BlogPost $blogPost)
    {
        abort_if(!$blogPost->is_published, 404);
        $related = BlogPost::where('is_published', true)
            ->where('id', '!=', $blogPost->id)
            ->latest()->take(3)->get();
        return view('blog.show', compact('blogPost', 'related'));
    }
}
