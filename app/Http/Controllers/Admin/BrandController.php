<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Services\SecureFileUploader;

class BrandController extends Controller
{
    /**
     * @var SecureFileUploader
     */
    protected $uploader;

    public function __construct(SecureFileUploader $uploader)
    {
        $this->uploader = $uploader;
    }

    public function index()
    {
        $brands = Brand::orderBy('sort_order')->orderBy('name')->paginate(10);
        return view('admin.brands.index', compact('brands'));
    }

    public function create()
    {
        return view('admin.brands.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'nullable|url|max:255',
            'logo' => 'nullable|image|max:2048', // 2MB Max
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('logo')) {
            $path = $this->uploader->upload($request->file('logo'), 'brands', 'tenant');
            $validated['logo'] = $path;
        }

        if (!$request->has('is_active')) {
            $validated['is_active'] = false;
        }

        $brand = Brand::create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'brand' => $brand,
                'message' => 'Brand created successfully.'
            ]);
        }

        return redirect()->route('admin.brands.index')->with('success', 'Brand created successfully.');
    }

    public function edit(Brand $brand)
    {
        return view('admin.brands.edit', compact('brand'));
    }

    public function update(Request $request, Brand $brand)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'nullable|url|max:255',
            'logo' => 'nullable|image|max:2048',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($brand->logo) {
                Storage::disk('tenant')->delete($brand->logo);
            }
            $path = $this->uploader->upload($request->file('logo'), 'brands', 'tenant');
            $validated['logo'] = $path;
        }
        
        // Handle checkbox
        $validated['is_active'] = $request->has('is_active');

        $brand->update($validated);

        return redirect()->route('admin.brands.index')->with('success', 'Brand updated successfully.');
    }

    public function destroy(Brand $brand)
    {
        if ($brand->logo) {
            Storage::disk('tenant')->delete($brand->logo);
        }
        $brand->delete();

        return redirect()->route('admin.brands.index')->with('success', 'Brand deleted successfully.');
    }
}
