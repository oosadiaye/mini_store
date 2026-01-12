@extends('admin.layout')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Bulk Image Upload</h2>
            <p class="text-sm text-gray-500 mt-1">Upload multiple product images at once. Images will be matched to products by SKU in the filename.</p>
        </div>
        <a href="{{ route('admin.products.index') }}" class="text-gray-600 hover:text-gray-900">
            ← Back to Products
        </a>
    </div>

    <!-- Instructions -->
    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-500"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">Filename Format</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <p class="mb-2">Name your image files using one of these patterns:</p>
                    <ul class="list-disc list-inside space-y-1">
                        <li><code class="bg-blue-100 px-1 rounded">SKU-123.jpg</code> - Direct SKU</li>
                        <li><code class="bg-blue-100 px-1 rounded">SKU-123-front.jpg</code> - SKU with suffix</li>
                        <li><code class="bg-blue-100 px-1 rounded">product_SKU-123.png</code> - Prefix with SKU</li>
                    </ul>
                    <p class="mt-2 text-xs">Maximum 50 images per upload, 5MB per image</p>
                </div>
            </div>
        </div>
    </div>

    <bulk-upload-manager
        upload-url="{{ route('admin.products.bulk-upload.store') }}"
        csrf-token="{{ csrf_token() }}"
    ></bulk-upload-manager>
</div>
@endsection
