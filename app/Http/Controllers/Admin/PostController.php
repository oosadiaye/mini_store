<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::latest()->paginate(10);
        return view('admin.posts.index', compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.posts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:posts,slug',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'nullable|string',
            'image' => 'nullable|image|max:2048', // Allow image upload
            'is_published' => 'boolean',
            'published_at' => 'nullable|date',
        ]);

        $post = new Post($validated);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('posts', 'public');
            $post->image_url = '/storage/' . $path;
        }

        $post->is_published = $request->has('is_published');
        $post->published_at = $post->is_published ? ($validated['published_at'] ?? now()) : null; // Default to now if published but no date
        
        // Auto slug handled by Model if empty, but we can respect input
        if (!empty($validated['slug'])) {
            $post->slug = Str::slug($validated['slug']);
        }

        $post->save();

        return redirect()->route('admin.posts.index')->with('success', 'Post created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        return view('admin.posts.edit', compact('post'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:posts,slug,' . $post->id,
            'excerpt' => 'nullable|string|max:500',
            'content' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'is_published' => 'boolean',
            'published_at' => 'nullable|date',
        ]);

        $post->fill($validated);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('posts', 'public');
            $post->image_url = '/storage/' . $path;
        }

        $post->is_published = $request->has('is_published');
        if ($post->is_published && !$post->published_at && $request->filled('published_at')) {
             $post->published_at = $request->published_at;
        } elseif ($post->is_published && !$post->published_at) {
             // If publishing now and no date set, assume now? Or keep existing if removing date?
             // Logic: If transitioning to published, set date.
             if ($post->wasChanged('is_published')) {
                 $post->published_at = now();
             }
        } elseif (!$post->is_published) {
             $post->published_at = null;
        }

        if (!empty($validated['slug'])) {
            $post->slug = Str::slug($validated['slug']);
        }

        $post->save();

        return redirect()->route('admin.posts.index')->with('success', 'Post updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        $post->delete();
        return back()->with('success', 'Post deleted successfully.');
    }
}
