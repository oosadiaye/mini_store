@extends('admin.layout')

@section('content')
<div class="max-w-4xl mx-auto px-3 md:px-0" x-data="productForm()">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 md:mb-6 gap-2">
        <h2 class="text-xl md:text-2xl font-bold text-gray-800">Add New Product</h2>
        <a href="{{ route('admin.products.index') }}" class="text-sm text-gray-600 hover:text-gray-900">← Back to Products</a>
    </div>

    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-lg shadow p-4 md:p-8">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Product Name -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Product Name *</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>

            <!-- Category -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                <div class="flex gap-2">
                    <select name="category_id" x-ref="categorySelect" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    <button type="button" @click="openCategoryModal = true" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-lg text-gray-600">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>

            <!-- Brand -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Brand</label>
                <div class="flex gap-2">
                    <select name="brand_id" x-ref="brandSelect" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        <option value="">Select Brand</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
                                {{ $brand->name }}
                            </option>
                        @endforeach
                    </select>
                    <button type="button" @click="openBrandModal = true" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-lg text-gray-600">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>

            <!-- SKU -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">SKU (Auto-generated if empty)</label>
                <input type="text" name="sku" value="{{ old('sku') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>

            <!-- Selling Price -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Selling Price *</label>
                <input type="number" name="price" value="{{ old('price') }}" step="0.01" min="0" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>

            <!-- Compare At Price -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Compare At Price</label>
                <input type="number" name="compare_at_price" value="{{ old('compare_at_price') }}" step="0.01" min="0"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>

            <!-- Cost Price -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Cost Price</label>
                <input type="number" name="cost_price" value="{{ old('cost_price') }}" step="0.01" min="0"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>

            <!-- Barcode -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Barcode</label>
                <input type="text" name="barcode" value="{{ old('barcode') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>

            <!-- Short Description -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Short Description</label>
                <textarea name="short_description" rows="2"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">{{ old('short_description') }}</textarea>
            </div>

            <!-- Description -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" rows="4"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">{{ old('description') }}</textarea>
            </div>

            <!-- Inventory -->
            <div class="md:col-span-2 border-t pt-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Inventory</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="track_inventory" value="1" {{ old('track_inventory') ? 'checked' : '' }}
                                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 mr-2">
                            <span class="text-sm font-medium text-gray-700">Track Inventory</span>
                        </label>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Stock Quantity</label>
                        <input type="number" name="stock_quantity" value="{{ old('stock_quantity', 0) }}" min="0"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Low Stock Threshold</label>
                        <input type="number" name="low_stock_threshold" value="{{ old('low_stock_threshold', 10) }}" min="0"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>
            </div>

            <!-- Images -->
            <div class="md:col-span-2 border-t pt-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Product Images</label>
                <div class="flex flex-col gap-4">
                    <div class="flex gap-2">
                        <input type="file" name="images[]" multiple accept="image/*" x-ref="imageInput"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        <button type="button" @click="openCamera()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 flex items-center gap-2 whitespace-nowrap">
                            <i class="fas fa-camera"></i> Use Camera
                        </button>
                    </div>
                    <p class="text-sm text-gray-500">You can select multiple images or capture them from your camera. First image will be the primary image.</p>
                    
                    <!-- Preview of captured/selected images could go here in future iteration -->
                </div>
            </div>

            <!-- Status -->
            <div class="md:col-span-2 border-t pt-6">
                <div class="flex items-center space-x-6">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 mr-2">
                        <span class="text-sm font-medium text-gray-700">Active</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}
                            class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 mr-2">
                        <span class="text-sm font-medium text-gray-700">Featured Product</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Submit -->
        <div class="mt-6 md:mt-8 flex flex-col sm:flex-row justify-end gap-3 sm:gap-4">
            <a href="{{ route('admin.products.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 text-center">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition">
                Create Product
            </button>
        </div>
    </form>

    <!-- Category Quick Create Modal -->
    <div x-show="openCategoryModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="openCategoryModal" @click="openCategoryModal = false" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="openCategoryModal" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Create New Category</h3>
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                                <input type="text" x-model="newCategoryName" @keydown.enter="createCategory()" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                <p x-show="categoryError" x-text="categoryError" class="text-red-500 text-xs mt-1"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" @click="createCategory()" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Create
                    </button>
                    <button type="button" @click="openCategoryModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Brand Quick Create Modal -->
    <div x-show="openBrandModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="openBrandModal" @click="openBrandModal = false" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="openBrandModal" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Create New Brand</h3>
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                                <input type="text" x-model="newBrandName" @keydown.enter="createBrand()" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                <p x-show="brandError" x-text="brandError" class="text-red-500 text-xs mt-1"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" @click="createBrand()" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Create
                    </button>
                    <button type="button" @click="openBrandModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Camera Modal -->
    <div x-show="showCameraModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showCameraModal" @click="closeCamera()" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="showCameraModal" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Capture Image</h3>
                    
                    <div class="relative bg-black rounded-lg overflow-hidden aspect-video mb-4">
                        <video x-ref="videoElement" class="w-full h-full object-contain" autoplay playsinline></video>
                        <canvas x-ref="canvasElement" class="hidden"></canvas>
                        <img x-show="capturedImage" :src="capturedImage" class="absolute inset-0 w-full h-full object-contain bg-black">
                    </div>

                    <div class="flex justify-center gap-4">
                        <button type="button" x-show="!capturedImage" @click="captureImage()" class="px-6 py-2 bg-indigo-600 text-white rounded-full hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <i class="fas fa-camera mr-2"></i> Capture
                        </button>
                        <button type="button" x-show="capturedImage" @click="retakeImage()" class="px-6 py-2 bg-gray-600 text-white rounded-full hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            <i class="fas fa-redo mr-2"></i> Retake
                        </button>
                        <button type="button" x-show="capturedImage" @click="saveImage()" class="px-6 py-2 bg-green-600 text-white rounded-full hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <i class="fas fa-check mr-2"></i> Use Photo
                        </button>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" @click="closeCamera()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function productForm() {
        return {
            openCategoryModal: false,
            newCategoryName: '',
            categoryError: '',
            
            openBrandModal: false,
            newBrandName: '',
            brandError: '',

            showCameraModal: false,
            stream: null,
            capturedImage: null,

            async openCamera() {
                this.showCameraModal = true;
                this.capturedImage = null;
                try {
                    this.stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
                    this.$refs.videoElement.srcObject = this.stream;
                } catch (err) {
                    console.error("Error accessing camera:", err);
                    alert("Could not access camera. Please ensure you have granted permission.");
                    this.showCameraModal = false;
                }
            },

            closeCamera() {
                if (this.stream) {
                    this.stream.getTracks().forEach(track => track.stop());
                }
                this.showCameraModal = false;
                this.stream = null;
            },

            captureImage() {
                const video = this.$refs.videoElement;
                const canvas = this.$refs.canvasElement;
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                const context = canvas.getContext('2d');
                context.drawImage(video, 0, 0, canvas.width, canvas.height);
                this.capturedImage = canvas.toDataURL('image/jpeg');
            },

            retakeImage() {
                this.capturedImage = null;
            },

            saveImage() {
                if (!this.capturedImage) return;

                // Convert data URL to Blob
                const byteString = atob(this.capturedImage.split(',')[1]);
                const mimeString = this.capturedImage.split(',')[0].split(':')[1].split(';')[0];
                const ab = new ArrayBuffer(byteString.length);
                const ia = new Uint8Array(ab);
                for (let i = 0; i < byteString.length; i++) {
                    ia[i] = byteString.charCodeAt(i);
                }
                const blob = new Blob([ab], {type: mimeString});
                const file = new File([blob], "camera-capture-" + Date.now() + ".jpg", {type: mimeString});

                // Add to input using DataTransfer
                const dataTransfer = new DataTransfer();
                
                // Keep existing files if any (optional, usually file input replaces)
                // If you want to append, you need to read existing files first.
                // Standard behavior for file input is replacement, but let's try to be friendly.
                const input = this.$refs.imageInput;
                if (input.files) {
                    Array.from(input.files).forEach(f => dataTransfer.items.add(f));
                }
                
                dataTransfer.items.add(file);
                input.files = dataTransfer.files;

                this.closeCamera();
            },

            async createCategory() {
                if (!this.newCategoryName.trim()) {
                    this.categoryError = 'Name is required';
                    return;
                }
                this.categoryError = '';

                try {
                    const response = await fetch('{{ route("admin.categories.store") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            name: this.newCategoryName,
                            is_active: true
                        })
                    });

                    const data = await response.json();

                    if (data.success) {
                        // Create option and select it
                        const select = this.$refs.categorySelect;
                        const option = new Option(data.category.name, data.category.id, true, true);
                        select.add(option);
                        
                        // Reset and close
                        this.newCategoryName = '';
                        this.openCategoryModal = false;
                        
                        // Force reactivity if needed, though native select update usually works
                    } else {
                        this.categoryError = data.message || 'Error creating category';
                        if (data.errors && data.errors.name) {
                            this.categoryError = data.errors.name[0];
                        }
                    }
                } catch (error) {
                    console.error('Error:', error);
                    this.categoryError = 'An unexpected error occurred';
                }
            },

            async createBrand() {
                if (!this.newBrandName.trim()) {
                    this.brandError = 'Name is required';
                    return;
                }
                this.brandError = '';

                try {
                    const response = await fetch('{{ route("admin.brands.store") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            name: this.newBrandName,
                            is_active: true
                        })
                    });

                    const data = await response.json();

                    if (data.success) {
                        // Create option and select it
                        const select = this.$refs.brandSelect;
                        const option = new Option(data.brand.name, data.brand.id, true, true);
                        select.add(option);
                        
                        // Reset and close
                        this.newBrandName = '';
                        this.openBrandModal = false;
                    } else {
                        this.brandError = data.message || 'Error creating brand';
                        if (data.errors && data.errors.name) {
                            this.brandError = data.errors.name[0];
                        }
                    }
                } catch (error) {
                    console.error('Error:', error);
                    this.brandError = 'An unexpected error occurred';
                }
            }
        }
    }
</script>
@endsection
