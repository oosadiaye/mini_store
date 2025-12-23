@extends('admin.layout')

@section('content')
<div class="max-w-4xl mx-auto" x-data="bulkUploadManager()">
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

    <!-- Upload Zone -->
    <div class="bg-white rounded-lg shadow p-8">
        <div 
            @dragover.prevent="dragOver = true"
            @dragleave="dragOver = false"
            @drop.prevent="handleDrop($event)"
            @click="$refs.fileInput.click()"
            :class="dragOver ? 'border-indigo-500 bg-indigo-50' : 'border-gray-300'"
            class="border-2 border-dashed rounded-lg p-12 text-center cursor-pointer transition-colors duration-200">
            
            <div x-show="!uploading && files.length === 0">
                <i class="fas fa-cloud-upload-alt text-6xl text-gray-400 mb-4"></i>
                <p class="text-lg font-medium text-gray-700 mb-2">Drag and drop images here</p>
                <p class="text-sm text-gray-500">or click to browse</p>
            </div>
            
            <div x-show="uploading" class="text-center">
                <i class="fas fa-spinner fa-spin text-6xl text-indigo-600 mb-4"></i>
                <p class="text-lg font-medium text-gray-700">Uploading images...</p>
                <p class="text-sm text-gray-500 mt-2" x-text="`Processing ${uploadProgress} of ${files.length} images`"></p>
            </div>
            
            <input type="file" x-ref="fileInput" @change="handleFileSelect($event)" accept="image/*" multiple class="hidden">
        </div>

        <!-- Preview Grid -->
        <div x-show="files.length > 0 && !uploading" class="mt-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Selected Images (<span x-text="files.length"></span>)</h3>
            <div class="grid grid-cols-4 gap-4 mb-6">
                <template x-for="(file, index) in files" :key="index">
                    <div class="relative group">
                        <img :src="file.preview" class="w-full h-24 object-cover rounded border">
                        <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity rounded flex items-center justify-center">
                            <button @click.stop="removeFile(index)" class="text-white hover:text-red-400">
                                <i class="fas fa-times-circle text-2xl"></i>
                            </button>
                        </div>
                        <p class="text-xs text-gray-600 mt-1 truncate" x-text="file.name"></p>
                        <p class="text-xs font-mono text-indigo-600" x-text="file.sku"></p>
                    </div>
                </template>
            </div>
            
            <button @click="uploadImages()" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-3 rounded-lg transition">
                <i class="fas fa-upload mr-2"></i> Upload <span x-text="files.length"></span> Images
            </button>
        </div>
    </div>

    <!-- Results -->
    <div x-show="results" class="mt-6 bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Upload Results</h3>
        
        <!-- Summary -->
        <div class="grid grid-cols-3 gap-4 mb-6">
            <div class="bg-green-50 p-4 rounded-lg">
                <p class="text-sm text-green-600 font-medium">Matched</p>
                <p class="text-2xl font-bold text-green-700" x-text="results?.summary?.matched || 0"></p>
            </div>
            <div class="bg-yellow-50 p-4 rounded-lg">
                <p class="text-sm text-yellow-600 font-medium">Unmatched</p>
                <p class="text-2xl font-bold text-yellow-700" x-text="results?.summary?.unmatched || 0"></p>
            </div>
            <div class="bg-red-50 p-4 rounded-lg">
                <p class="text-sm text-red-600 font-medium">Errors</p>
                <p class="text-2xl font-bold text-red-700" x-text="results?.summary?.errors || 0"></p>
            </div>
        </div>

        <!-- Matched Images -->
        <div x-show="results?.results?.matched?.length > 0" class="mb-6">
            <h4 class="font-medium text-green-700 mb-2">✓ Successfully Uploaded</h4>
            <div class="space-y-2">
                <template x-for="item in results.results.matched" :key="item.filename">
                    <div class="flex items-center justify-between bg-green-50 p-3 rounded">
                        <div class="flex items-center">
                            <img :src="item.url" class="w-12 h-12 object-cover rounded mr-3">
                            <div>
                                <p class="text-sm font-medium text-gray-900" x-text="item.product_name"></p>
                                <p class="text-xs text-gray-500">SKU: <span x-text="item.sku"></span> • <span x-text="item.filename"></span></p>
                            </div>
                        </div>
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                </template>
            </div>
        </div>

        <!-- Unmatched Images -->
        <div x-show="results?.results?.unmatched?.length > 0" class="mb-6">
            <h4 class="font-medium text-yellow-700 mb-2">⚠ No Matching Product Found</h4>
            <div class="space-y-2">
                <template x-for="item in results.results.unmatched" :key="item.filename">
                    <div class="flex items-center justify-between bg-yellow-50 p-3 rounded">
                        <div>
                            <p class="text-sm font-medium text-gray-900" x-text="item.filename"></p>
                            <p class="text-xs text-gray-500">Extracted SKU: <span x-text="item.sku"></span></p>
                        </div>
                        <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                    </div>
                </template>
            </div>
        </div>

        <!-- Errors -->
        <div x-show="results?.results?.errors?.length > 0">
            <h4 class="font-medium text-red-700 mb-2">✗ Upload Errors</h4>
            <div class="space-y-2">
                <template x-for="item in results.results.errors" :key="item.filename">
                    <div class="bg-red-50 p-3 rounded">
                        <p class="text-sm font-medium text-gray-900" x-text="item.filename"></p>
                        <p class="text-xs text-red-600" x-text="item.error"></p>
                    </div>
                </template>
            </div>
        </div>

        <button @click="reset()" class="mt-6 w-full bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 rounded-lg transition">
            Upload More Images
        </button>
    </div>
</div>

<script>
function bulkUploadManager() {
    return {
        files: [],
        dragOver: false,
        uploading: false,
        uploadProgress: 0,
        results: null,
        
        handleDrop(event) {
            this.dragOver = false;
            const droppedFiles = Array.from(event.dataTransfer.files);
            this.addFiles(droppedFiles);
        },
        
        handleFileSelect(event) {
            const selectedFiles = Array.from(event.target.files);
            this.addFiles(selectedFiles);
        },
        
        addFiles(newFiles) {
            const imageFiles = newFiles.filter(f => f.type.startsWith('image/'));
            
            imageFiles.forEach(file => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.files.push({
                        file: file,
                        name: file.name,
                        preview: e.target.result,
                        sku: this.extractSku(file.name)
                    });
                };
                reader.readAsDataURL(file);
            });
        },
        
        extractSku(filename) {
            const name = filename.replace(/\.[^/.]+$/, '');
            const patterns = [
                /^([A-Z0-9\-_]+)$/i,
                /^([A-Z0-9\-_]+?)[\-_](front|back|side|image|photo|main|\d+)/i,
                /[_\-]([A-Z0-9\-_]+)$/i
            ];
            
            for (let pattern of patterns) {
                const match = name.match(pattern);
                if (match) return match[1].toUpperCase();
            }
            return name.toUpperCase();
        },
        
        removeFile(index) {
            this.files.splice(index, 1);
        },
        
        async uploadImages() {
            this.uploading = true;
            this.uploadProgress = 0;
            
            const formData = new FormData();
            this.files.forEach((fileObj, index) => {
                formData.append(`images[${index}]`, fileObj.file);
            });
            
            try {
                const response = await fetch('{{ route("admin.products.bulk-upload.store") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                
                const data = await response.json();
                this.results = data;
                this.files = [];
            } catch (error) {
                console.error('Upload error:', error);
                alert('Upload failed. Please try again.');
            } finally {
                this.uploading = false;
            }
        },
        
        reset() {
            this.files = [];
            this.results = null;
            this.uploadProgress = 0;
        }
    }
}
</script>
@endsection
