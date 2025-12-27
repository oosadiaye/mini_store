<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\PageSection;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PageController extends Controller
{
    public function index()
    {
        $pages = Page::orderBy('sort_order')->get();
        return view('admin.pages.index', compact('pages'));
    }

    public function create()
    {
        return view('admin.pages.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:pages,slug',
        ]);

        $page = Page::create($request->all());

        return redirect()->route('admin.pages.builder', $page->id);
    }

    public function edit(Page $page)
    {
        return view('admin.pages.edit', compact('page'));
    }

    public function update(Request $request, Page $page)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:pages,slug,' . $page->id,
            'content' => 'nullable|string',
            'is_published' => 'boolean',
        ]);

        $page->update([
            'title' => $request->title,
            'slug' => $request->slug,
            'content' => $request->content, // Assuming 'content' column exists? Or keeps 'body'?
            'is_published' => $request->has('is_published'),
        ]);

        return redirect()->route('admin.pages.index')->with('success', 'Page updated successfully.');
    }
    
    public function destroy(Page $page) {
        $page->delete();
        return redirect()->route('admin.pages.index')->with('success', 'Page deleted.');
    }
}
