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

    public function builder(Page $page)
    {
        $page->load(['sections' => function($q) {
            $q->orderBy('sort_order');
        }]);
        return view('admin.pages.builder', compact('page'));
    }

    public function storeSection(Request $request, Page $page)
    {
        $request->validate([
            'section_type' => 'required|string',
        ]);

        $count = $page->sections()->count();

        $section = $page->sections()->create([
            'section_type' => $request->section_type,
            'title' => 'New ' . ucfirst(str_replace('_', ' ', $request->section_type)),
            'sort_order' => $count + 1,
            'settings' => [],
            'is_active' => true,
        ]);

        return back()->with('success', 'Section added successfully.');
    }

    public function updateSection(Request $request, Page $page, PageSection $section)
    {
        $data = $request->except(['_token', '_method']);
        
        // Handle file uploads inside settings array
        if ($request->hasFile('settings')) {
            $files = $request->file('settings');
            foreach ($files as $key => $file) {
                // Store file and get path
                $path = $file->store('page-assets/' . $page->id, 'public');
                // We need to merge this into the data['settings'] array properly
                // But $data['settings'] might not have this key if it's a file input, 
                // it comes as a UploadedFile object in the request->file bag, 
                // but might not be in the $request->input('settings') array if not processed.
                
                // Add the path to the settings array to be saved
                $data['settings'][$key] = $path;
            }
        }

        // Handle specific settings logic if needed
        if (isset($data['settings'])) {
            $currentSettings = $section->settings ?? [];
            // Merge new settings with existing ones
            // Note: array_merge simply overwrites existing keys. 
            // If we didn't upload a new file, we shouldn't overwrite the old path with null/missing.
            // But since input type='file' usually doesn't send anything if empty, we are good.
            $section->settings = array_merge($currentSettings, $data['settings']);
        } else {
             // If no settings passed (e.g. only title update), keep existing
        }
        
        // Remove settings from data to avoid double assignment or issues if we handled it manually above
        unset($data['settings']);

        $section->update($data);
        $section->save(); // Explicit save to ensure JSON casting works if needed

        return back()->with('success', 'Section updated successfully.');
    }

    public function deleteSection(Page $page, PageSection $section)
    {
        $section->delete();
        return back()->with('success', 'Section removed.');
    }

    public function reorderSections(Request $request, Page $page)
    {
        $order = $request->order; // Array of section IDs in new order
        
        foreach ($order as $index => $id) {
            PageSection::where('id', $id)->update(['sort_order' => $index]);
        }
        
        return response()->json(['success' => true]);
    }
    
    public function update(Request $request, Page $page) {
        $page->update($request->all());
        return back()->with('success', 'Page settings updated.');
    }

    public function destroy(Page $page) {
        $page->delete();
        return redirect()->route('admin.pages.index')->with('success', 'Page deleted.');
    }
}
