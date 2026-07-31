<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    public function index()
    {
        $posts = BlogPost::latest()->paginate(20);
        return view('admin.blog.index', compact('posts'));
    }

    public function create()
    {
        return view('admin.blog.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'featured_image' => 'nullable|url',
            'author' => 'nullable|string|max:100',
            'is_published' => 'boolean',
        ]);

        BlogPost::create([
            'title' => $request->title,
            'slug' => Str::slug($request->title) . '-' . Str::random(4),
            'content' => $request->content,
            'excerpt' => $request->excerpt,
            'featured_image' => $request->featured_image,
            'author' => $request->author ?? 'BeeG Events',
            'is_published' => $request->boolean('is_published'),
            'published_at' => $request->boolean('is_published') ? now() : null,
        ]);

        return redirect()->route('admin.blog.index')->with('success', 'Blog post created!');
    }

    public function edit(BlogPost $blogPost)
    {
        return view('admin.blog.form', compact('blogPost'));
    }

    public function update(Request $request, BlogPost $blogPost)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'featured_image' => 'nullable|url',
            'author' => 'nullable|string|max:100',
            'is_published' => 'boolean',
        ]);

        $blogPost->update([
            'title' => $request->title,
            'content' => $request->content,
            'excerpt' => $request->excerpt,
            'featured_image' => $request->featured_image,
            'author' => $request->author ?? 'BeeG Events',
            'is_published' => $request->boolean('is_published'),
            'published_at' => $request->boolean('is_published')
                ? ($blogPost->published_at ?: now())
                : null,
        ]);

        return redirect()->route('admin.blog.index')->with('success', 'Blog post updated!');
    }

    public function destroy(BlogPost $blogPost)
    {
        $blogPost->delete();
        return redirect()->route('admin.blog.index')->with('success', 'Blog post deleted!');
    }
}
