@extends('admin.layout')

@section('content')
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4 md:mb-6 gap-3" x-data="{ showImport: false }">
    <h2 class="text-xl md:text-2xl font-bold text-gray-800">Products</h2>
    <div class="flex gap-2 w-full md:w-auto">
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" @click.away="open = false" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg transition border border-gray-300 flex items-center gap-2">
                <i class="fas fa-file-export"></i> Export / Import
                <i class="fas fa-chevron-down text-xs ml-1"></i>
            </button>
            <div x-show="open" style="display: none;" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 border border-gray-100">
                <a href="{{ route('admin.products.export') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                    <i class="fas fa-download mr-2"></i> Export CSV
                </a>
                <a href="{{ route('admin.products.template') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                    <i class="fas fa-file-csv mr-2"></i> Download Template
                </a>
                <button @click="showImport = true; open = false" class="w-full text-left block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                    <i class="fas fa-file-upload mr-2"></i> Import CSV
                </button>
                <a href="{{ route('admin.products.bulk-upload.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                    <i class="fas fa-images mr-2"></i> Bulk Upload Images
                </a>
            </div>
        </div>
        <a href="{{ route('admin.products.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 md:px-6 py-2 rounded-lg transition flex-1 md:flex-none text-center">
            + Add Product
        </a>
    </div>

    <!-- Import Modal -->
    <div x-show="showImport" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showImport" @click="showImport = false" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="showImport" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="{{ route('admin.products.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                                <i class="fas fa-file-import text-blue-600"></i>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Import Products</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 mb-4">
                                        Upload a CSV file or ZIP file (containing CSV + images folder) to import products. 
                                        Existing products with matching SKU will be updated.
                                    </p>
                                    <div class="bg-blue-50 border-l-4 border-blue-400 p-3 mb-4">
                                        <p class="text-xs text-blue-700 font-medium mb-1">ZIP Import Structure:</p>
                                        <pre class="text-xs text-blue-600 font-mono">products.zip
├── products.csv
└── images/
    ├── SKU-001.jpg
    └── SKU-002.png</pre>
                                    </div>
                                    <div class="mt-2">
                                        <input type="file" name="file" accept=".csv,.txt,.zip" required class="w-full border p-2 rounded bg-gray-50 text-sm">
                                    </div>
                                    <div class="mt-4 text-xs text-gray-400">
                                        <a href="{{ route('admin.products.template') }}" class="text-blue-600 hover:underline">Don't have a template? Download here.</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                            Import Data
                        </button>
                        <button type="button" @click="showImport = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Search and Filters -->
<div class="bg-white rounded-lg shadow p-3 md:p-6 mb-4 md:mb-6">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products..." 
            class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
        
        <select name="category_id" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            <option value="">All Categories</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>

        <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            <option value="">All Status</option>
            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
            <option value="low_stock" {{ request('status') == 'low_stock' ? 'selected' : '' }}>Low Stock</option>
        </select>

        <select name="warehouse_id" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            <option value="">All Warehouses</option>
            @foreach($warehouses as $wh)
                <option value="{{ $wh->id }}" {{ request('warehouse_id') == $wh->id ? 'selected' : '' }}>
                    {{ $wh->name }}
                </option>
            @endforeach
        </select>

        <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg transition">
            Filter
        </button>
    </form>
</div>

<!-- Bulk Actions Bar -->
<div class="bg-white rounded-lg shadow p-2 md:p-4 mb-3 md:mb-4" x-data="{ 
    selectedProducts: [], 
    showFlashSaleModal: false,
    flashSalePrice: '',
    flashSaleStart: '',
    flashSaleEnd: '',
    selectAll: false,
    toggleAll() {
        this.selectAll = !this.selectAll;
        const checkboxes = document.querySelectorAll('.product-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = this.selectAll;
            const id = parseInt(cb.value);
            if (this.selectAll && !this.selectedProducts.includes(id)) {
                this.selectedProducts.push(id);
            } else if (!this.selectAll) {
                this.selectedProducts = [];
            }
        });
    },
    toggleProduct(id) {
        if (this.selectedProducts.includes(id)) {
            this.selectedProducts = this.selectedProducts.filter(p => p !== id);
        } else {
            this.selectedProducts.push(id);
        }
    },
    submitBulkAction(action) {
        if (this.selectedProducts.length === 0) {
            alert('Please select at least one product');
            return;
        }
        
        if (action === 'enable_flash_sale') {
            this.showFlashSaleModal = true;
            return;
        }
        
        if (action === 'delete' && !confirm('Are you sure you want to delete ' + this.selectedProducts.length + ' products?')) {
            return;
        }
        
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route('admin.products.bulk-action') }}';
        
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '{{ csrf_token() }}';
        form.appendChild(csrfInput);
        
        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        actionInput.value = action;
        form.appendChild(actionInput);
        
        this.selectedProducts.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'product_ids[]';
            input.value = id;
            form.appendChild(input);
        });
        
        document.body.appendChild(form);
        form.submit();
    },
    submitFlashSale() {
        if (!this.flashSalePrice || !this.flashSaleStart || !this.flashSaleEnd) {
            alert('Please fill all flash sale fields');
            return;
        }
        
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route('admin.products.bulk-action') }}';
        
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '{{ csrf_token() }}';
        form.appendChild(csrfInput);
        
        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        actionInput.value = 'enable_flash_sale';
        form.appendChild(actionInput);
        
        ['flash_sale_price', 'flash_sale_start', 'flash_sale_end'].forEach(field => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = field;
            input.value = this[field.replace('flash_sale_', 'flashSale' + field.split('_').slice(2).map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(''))];
            form.appendChild(input);
        });
        
        this.selectedProducts.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'product_ids[]';
            input.value = id;
            form.appendChild(input);
        });
        
        document.body.appendChild(form);
        form.submit();
    }
}">
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-2">
        <div class="flex items-center gap-2 md:gap-4 w-full md:w-auto">
            <span class="text-xs md:text-sm text-gray-600">
                <span x-text="selectedProducts.length"></span> selected
            </span>
            
            <select @change="submitBulkAction($event.target.value); $event.target.value = ''" 
                class="border border-gray-300 rounded-lg px-2 md:px-4 py-1.5 md:py-2 text-xs md:text-sm focus:ring-2 focus:ring-indigo-500 flex-1 md:flex-none"
                :disabled="selectedProducts.length === 0">
                <option value="">Bulk Actions</option>
                <option value="mark_featured">⭐ Mark as Featured</option>
                <option value="unmark_featured">Remove Featured</option>
                <option value="enable_flash_sale">🔥 Enable Flash Sale</option>
                <option value="disable_flash_sale">Disable Flash Sale</option>
                <option value="activate">✅ Activate</option>
                <option value="deactivate">❌ Deactivate</option>
                <option value="delete">🗑️ Delete</option>
            </select>
        </div>
    </div>
    
    <!-- Flash Sale Modal -->
    <div x-show="showFlashSaleModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" @click.away="showFlashSaleModal = false">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75"></div>
            <div class="relative bg-white rounded-lg p-6 max-w-md w-full">
                <h3 class="text-lg font-bold mb-4">Set Flash Sale</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Flash Sale Price</label>
                        <input type="number" x-model="flashSalePrice" step="0.01" 
                            class="w-full border border-gray-300 rounded-lg px-4 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Start Date & Time</label>
                        <input type="datetime-local" x-model="flashSaleStart" 
                            class="w-full border border-gray-300 rounded-lg px-4 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">End Date & Time</label>
                        <input type="datetime-local" x-model="flashSaleEnd" 
                            class="w-full border border-gray-300 rounded-lg px-4 py-2">
                    </div>
                    <div class="flex gap-2 pt-4">
                        <button @click="submitFlashSale()" 
                            class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg">
                            Apply Flash Sale
                        </button>
                        <button @click="showFlashSaleModal = false" 
                            class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Products Table (Desktop) -->
<div class="hidden lg:block bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left">
                    <input type="checkbox" @change="toggleAll()" 
                        class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">SKU</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($products as $product)
                <tr x-data="productImageUpload({{ $product->id }})">
                    <td class="px-6 py-4">
                        <input type="checkbox" value="{{ $product->id }}" 
                            class="product-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                            @change="toggleProduct({{ $product->id }})">
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="relative group mr-3">
                                <!-- Image/Placeholder -->
                                @if($product->primaryImage())
                                    <img :src="imageUrl || '{{ $product->primaryImage()->url }}'" alt="{{ $product->name }}" class="h-10 w-10 rounded object-cover">
                                @else
                                    <div class="h-10 w-10 bg-gray-200 rounded flex items-center justify-center text-gray-400">
                                        <span x-show="!imageUrl">📦</span>
                                        <img x-show="imageUrl" :src="imageUrl" class="h-10 w-10 rounded object-cover">
                                    </div>
                                @endif
                                
                                <!-- Drag & Drop Overlay -->
                                <div 
                                    @dragover.prevent="dragOver = true"
                                    @dragleave="dragOver = false"
                                    @drop.prevent="handleDrop($event)"
                                    @click="$refs.fileInput.click()"
                                    :class="dragOver ? 'opacity-100' : 'opacity-0 group-hover:opacity-100'"
                                    class="absolute inset-0 bg-black bg-opacity-60 rounded flex items-center justify-center cursor-pointer transition-opacity duration-200">
                                    <i class="fas fa-upload text-white text-xs" x-show="!uploading"></i>
                                    <i class="fas fa-spinner fa-spin text-white text-xs" x-show="uploading"></i>
                                </div>
                                
                                <!-- Hidden File Input -->
                                <input type="file" x-ref="fileInput" @change="handleFileSelect($event)" accept="image/*" class="hidden">
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                @if($product->is_featured)
                                    <span class="text-xs text-yellow-600">⭐ Featured</span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $product->sku }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $product->category->name ?? '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                        {{ $tenant->data['currency_symbol'] ?? '₦' }}{{ number_format($product->price, 2) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($product->track_inventory)
                            <span class="text-sm {{ $product->isLowStock() ? 'text-red-600 font-bold' : 'text-gray-900' }}">
                                {{ $product->stock_quantity }}
                            </span>
                        @else
                            <span class="text-sm text-gray-400">N/A</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($product->is_active)
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                        @else
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Inactive</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('admin.products.edit', $product) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                        <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                        No products found. <a href="{{ route('admin.products.create') }}" class="text-indigo-600 hover:underline">Create your first product</a>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Products Cards (Mobile) -->
<div class="lg:hidden space-y-3">
    @forelse($products as $product)
        <div class="bg-white rounded-lg shadow p-3" x-data="productImageUpload({{ $product->id }})">
            <!-- Card Header -->
            <div class="flex items-start justify-between mb-2">
                <div class="flex items-center gap-2">
                    <input type="checkbox" value="{{ $product->id }}" 
                        class="product-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                        @change="toggleProduct({{ $product->id }})">
                    <h3 class="text-sm font-semibold text-gray-900 line-clamp-2">{{ $product->name }}</h3>
                </div>
                @if($product->is_featured)
                    <span class="text-lg">⭐</span>
                @endif
            </div>
            
            <!-- Card Body -->
            <div class="flex gap-3">
                <!-- Product Image -->
                <div class="relative group flex-shrink-0">
                    @if($product->primaryImage())
                        <img :src="imageUrl || '{{ $product->primaryImage()->url }}'" alt="{{ $product->name }}" class="h-16 w-16 rounded object-cover">
                    @else
                        <div class="h-16 w-16 bg-gray-200 rounded flex items-center justify-center text-gray-400">
                            <span x-show="!imageUrl" class="text-2xl">📦</span>
                            <img x-show="imageUrl" :src="imageUrl" class="h-16 w-16 rounded object-cover">
                        </div>
                    @endif
                    
                    <!-- Drag & Drop Overlay -->
                    <div 
                        @dragover.prevent="dragOver = true"
                        @dragleave="dragOver = false"
                        @drop.prevent="handleDrop($event)"
                        @click="$refs.fileInput.click()"
                        :class="dragOver ? 'opacity-100' : 'opacity-0 group-hover:opacity-100'"
                        class="absolute inset-0 bg-black bg-opacity-60 rounded flex items-center justify-center cursor-pointer transition-opacity duration-200">
                        <i class="fas fa-upload text-white text-sm" x-show="!uploading"></i>
                        <i class="fas fa-spinner fa-spin text-white text-sm" x-show="uploading"></i>
                    </div>
                    
                    <!-- Hidden File Input -->
                    <input type="file" x-ref="fileInput" @change="handleFileSelect($event)" accept="image/*" class="hidden">
                </div>
                
                <!-- Product Details -->
                <div class="flex-1 min-w-0 space-y-1">
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-gray-500">SKU:</span>
                        <span class="text-xs font-medium text-gray-900">{{ $product->sku }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-gray-500">Category:</span>
                        <span class="text-xs text-gray-700">{{ $product->category->name ?? '-' }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-gray-500">Price:</span>
                        <span class="text-sm font-bold text-gray-900">{{ app('tenant')->data['currency_symbol'] ?? '₦' }}{{ number_format($product->price, 2) }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-gray-500">Stock:</span>
                        @if($product->track_inventory)
                            <span class="text-xs {{ $product->isLowStock() ? 'text-red-600 font-bold' : 'text-gray-900' }}">
                                {{ $product->stock_quantity }} units
                            </span>
                        @else
                            <span class="text-xs text-gray-400">N/A</span>
                        @endif
                    </div>
                    <div class="flex items-center gap-2">
                        @if($product->is_active)
                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                        @else
                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Inactive</span>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Card Footer -->
            <div class="flex gap-2 mt-3 pt-3 border-t border-gray-200">
                <a href="{{ route('admin.products.edit', $product) }}" class="flex-1 text-center bg-indigo-50 hover:bg-indigo-100 text-indigo-600 px-3 py-2 rounded text-sm font-medium transition">
                    <i class="fas fa-edit mr-1"></i> Edit
                </a>
                <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="flex-1" onsubmit="return confirm('Are you sure?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full bg-red-50 hover:bg-red-100 text-red-600 px-3 py-2 rounded text-sm font-medium transition">
                        <i class="fas fa-trash mr-1"></i> Delete
                    </button>
                </form>
            </div>
        </div>
    @empty
        <div class="bg-white rounded-lg shadow p-8 text-center">
            <p class="text-gray-500 mb-3">No products found.</p>
            <a href="{{ route('admin.products.create') }}" class="text-indigo-600 hover:underline font-medium">Create your first product</a>
        </div>
    @endforelse
</div>

<!-- Pagination -->
<div class="mt-6">
    {{ $products->links() }}
</div>

<script>
function productImageUpload(productId) {
    return {
        uploading: false,
        dragOver: false,
        imageUrl: null,
        
        handleDrop(event) {
            this.dragOver = false;
            const file = event.dataTransfer.files[0];
            if (file && file.type.startsWith('image/')) {
                this.uploadImage(file);
            }
        },
        
        handleFileSelect(event) {
            const file = event.target.files[0];
            if (file) {
                this.uploadImage(file);
            }
        },
        
        async uploadImage(file) {
            this.uploading = true;
            const formData = new FormData();
            formData.append('image', file);
            formData.append('_token', '{{ csrf_token() }}');
            
            try {
                const response = await fetch(`/admin/products/${productId}/quick-image`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.imageUrl = data.url;
                    window.showToast && window.showToast('Image uploaded successfully!');
                } else {
                    alert('Upload failed. Please try again.');
                }
            } catch (error) {
                console.error('Upload error:', error);
                alert('Upload failed. Please try again.');
            } finally {
                this.uploading = false;
            }
        }
    }
}
</script>
@endsection
