<template>
  <div class="max-w-4xl mx-auto px-3 md:px-0">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 md:mb-6 gap-2">
        <h2 class="text-xl md:text-2xl font-bold text-gray-800">{{ isEdit ? 'Edit Product' : 'Add New Product' }}</h2>
        <a :href="routes.index" class="text-sm text-gray-600 hover:text-gray-900">← Back to Products</a>
    </div>

    <form :action="actionUrl" method="POST" enctype="multipart/form-data" class="bg-white rounded-lg shadow p-4 md:p-8">
        <input type="hidden" name="_token" :value="csrfToken">
        <input type="hidden" name="_method" value="PUT" v-if="isEdit">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Product Name -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Product Name *</label>
                <input type="text" name="name" v-model="form.name" required
                    :class="['w-full px-4 py-2 border-2 rounded-lg focus:ring-2 focus:ring-indigo-500', errorClass('name')]"
                >
                <p v-if="errors.name" class="text-red-500 text-xs mt-1">{{ errors.name[0] }}</p>
            </div>

            <!-- Category -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                <div class="flex gap-2">
                    <select name="category_id" v-model="form.category_id" class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        <option value="">Select Category</option>
                        <option v-for="category in localCategories" :key="category.id" :value="category.id">
                            {{ category.name }}
                        </option>
                    </select>
                    <button type="button" @click="showCategoryModal = true" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 border-2 border-gray-300 rounded-lg text-gray-600">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>

            <!-- Brand -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Brand</label>
                <div class="flex gap-2">
                    <select name="brand_id" v-model="form.brand_id" class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        <option value="">Select Brand</option>
                        <option v-for="brand in localBrands" :key="brand.id" :value="brand.id">
                            {{ brand.name }}
                        </option>
                    </select>
                    <button type="button" @click="showBrandModal = true" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 border-2 border-gray-300 rounded-lg text-gray-600">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>

            <!-- SKU -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Serial Number (Auto-generated if empty)</label>
                <input type="text" name="sku" v-model="form.sku"
                    :class="['w-full px-4 py-2 border-2 rounded-lg focus:ring-2 focus:ring-indigo-500', errorClass('sku')]"
                >
                <p v-if="errors.sku" class="text-red-500 text-xs mt-1">{{ errors.sku[0] }}</p>
            </div>

            <!-- Selling Price -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Selling Price *</label>
                <input type="number" name="price" v-model="form.price" step="0.01" min="0" required
                    :class="['w-full px-4 py-2 border-2 rounded-lg focus:ring-2 focus:ring-indigo-500', errorClass('price')]"
                >
                <p v-if="errors.price" class="text-red-500 text-xs mt-1">{{ errors.price[0] }}</p>
            </div>

            <!-- Flash Sale Config (Edit Only) -->
            <div v-if="isEdit" class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6 p-4 bg-red-50 rounded-lg border border-red-100">
                 <div>
                     <label class="block text-sm font-medium text-red-600 mb-2">⚡ Flash Sale Price</label>
                     <input type="number" name="flash_sale_price" v-model="form.flash_sale_price" step="0.01" min="0"
                         class="w-full px-4 py-2 border-2 border-red-300 rounded-lg focus:ring-2 focus:ring-red-500 bg-white" placeholder="Optional override">
                 </div>
                 <div>
                     <label class="block text-sm font-medium text-red-600 mb-2">⚡ Flash Sale Ends At</label>
                     <input type="datetime-local" name="flash_sale_end_date" v-model="form.flash_sale_end_date"
                         class="w-full px-4 py-2 border-2 border-red-300 rounded-lg focus:ring-2 focus:ring-red-500 bg-white">
                 </div>
            </div>

            <!-- Compare At Price -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Compare At Price</label>
                <input type="number" name="compare_at_price" v-model="form.compare_at_price" step="0.01" min="0"
                    class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>

            <!-- Cost Price -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Cost Price</label>
                <input type="number" name="cost_price" v-model="form.cost_price" step="0.01" min="0" @input="calculatePrice"
                    class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>

            <!-- Profit Margin -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Profit Margin (%)</label>
                <input type="number" v-model="profit_margin" step="0.01" @input="calculatePrice" placeholder="Enter % to calc price"
                    class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>

            <!-- Barcode -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Barcode</label>
                <div class="flex gap-2">
                    <input type="text" name="barcode" v-model="form.barcode"
                        class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <button type="button" @click="startBarcodeScanner" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 border-2 border-gray-300 rounded-lg text-gray-600 whitespace-nowrap">
                        <i class="fas fa-barcode mr-1"></i> Scan
                    </button>
                </div>
            </div>

             <!-- Description Fields -->
             <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Short Description</label>
                <textarea name="short_description" v-model="form.short_description" rows="2"
                    class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"></textarea>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" v-model="form.description" rows="4"
                    class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"></textarea>
            </div>

            <!-- Inventory -->
            <div class="md:col-span-2 border-t pt-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Inventory</h3>
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="track_inventory" value="1" v-model="form.track_inventory"
                                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 mr-2">
                            <span class="text-sm font-medium text-gray-700">Track Inventory</span>
                        </label>
                    </div>

                    <!-- Warehouse Stock -->
                    <div v-if="warehouses.length > 0" class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <h4 class="text-sm font-bold text-gray-700 mb-3">Stock by Warehouse</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div v-for="warehouse in warehouses" :key="warehouse.id">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    {{ warehouse.name }}
                                    <span class="text-xs text-gray-500">({{ warehouse.code }})</span>
                                </label>
                                <input type="number" 
                                       :name="`warehouse_stock[${warehouse.id}]`" 
                                       v-model="form.warehouse_stock[warehouse.id]" 
                                       min="0"
                                       class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                            </div>
                        </div>
                         <div class="mt-4 p-3 bg-indigo-50 rounded border border-indigo-200">
                            <p class="text-sm text-indigo-800">
                                <strong>Total Stock:</strong> 
                                <span>{{ calculatedTotalStock }}</span> units
                            </p>
                        </div>
                    </div>
                    
                    <div v-else>
                         <label class="block text-sm font-medium text-gray-700 mb-2">Stock Quantity</label>
                         <input type="number" name="stock_quantity" v-model="form.stock_quantity" min="0" ref="totalStockInput"
                            class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                         <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Low Stock Threshold</label>
                            <input type="number" name="low_stock_threshold" v-model="form.low_stock_threshold" min="0"
                                class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Expiry Date</label>
                            <input type="date" name="expiry_date" v-model="form.expiry_date"
                                class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Existing Images (Edit only) -->
            <div v-if="isEdit && existingImages.length > 0" class="md:col-span-2 border-t pt-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Current Images</label>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div v-for="image in existingImages" :key="image.id" class="relative group">
                        <img :src="image.url" class="h-32 w-full object-cover rounded-lg border border-gray-200">
                        <span v-if="image.is_primary" class="absolute top-2 left-2 bg-indigo-600 text-white text-xs px-2 py-1 rounded">Primary</span>
                        <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition flex items-center justify-center rounded-lg">
                             <a :href="`${routes.deleteImageBase}/${image.id}`" 
                                onclick="event.preventDefault(); if(confirm('Delete this image?')) document.getElementById('delete-img-' + this.getAttribute('data-id')).submit()"
                                :data-id="image.id"
                                class="text-white hover:text-red-400 cursor-pointer">
                                <i class="fas fa-trash text-2xl"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <!-- Hidden forms for image deletion (legacy support) -->
                 <div v-for="image in existingImages" :key="`form-${image.id}`" class="hidden">
                     <form :id="`delete-img-${image.id}`" :action="`${routes.deleteImageBase}/${image.id}`" method="POST">
                         <input type="hidden" name="_token" :value="csrfToken">
                         <input type="hidden" name="_method" value="DELETE">
                     </form>
                 </div>
            </div>

            <!-- Images -->
            <div class="md:col-span-2 border-t pt-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ isEdit ? 'Add More Images' : 'Product Images' }}
                </label>
                <div class="flex flex-col gap-4">
                    <div class="flex gap-2">
                        <input type="file" ref="imageInput" name="images[]" multiple accept="image/*"
                            class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        <button type="button" @click="openCamera" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 flex items-center gap-2 whitespace-nowrap">
                            <i class="fas fa-camera"></i> Use Camera
                        </button>
                    </div>
                    <p class="text-sm text-gray-500">You can select multiple images or capture them from your camera. First image will be the primary image.</p>
                </div>
            </div>

            <!-- Status -->
            <div class="md:col-span-2 border-t pt-6">
                <div class="flex items-center space-x-6">
                     <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" v-model="form.is_active"
                            class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 mr-2">
                        <span class="text-sm font-medium text-gray-700">Active</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_featured" value="1" v-model="form.is_featured"
                            class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 mr-2">
                        <span class="text-sm font-medium text-gray-700">Featured Product</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Submit -->
        <div class="mt-6 md:mt-8 flex flex-col sm:flex-row justify-end gap-3 sm:gap-4">
            <a :href="routes.index" class="px-6 py-2 border-2 border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 text-center">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition">
                {{ isEdit ? 'Update Product' : 'Create Product' }}
            </button>
        </div>
    </form>

    <!-- Category Modal -->
    <CommonModal :show="showCategoryModal" title="Create New Category" @close="showCategoryModal = false">
        <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
            <input type="text" v-model="newCategoryName" @keydown.enter="createCategory" class="w-full px-3 py-2 border-2 border-gray-300 rounded-md">
            <p v-if="categoryError" class="text-red-500 text-xs mt-1">{{ categoryError }}</p>
        </div>
        <div class="mt-4 flex justify-end gap-2">
             <button @click="showCategoryModal = false" class="px-4 py-2 bg-gray-200 rounded text-gray-700">Cancel</button>
             <button @click="createCategory" class="px-4 py-2 bg-indigo-600 text-white rounded">Create</button>
        </div>
    </CommonModal>

    <!-- Brand Modal -->
    <CommonModal :show="showBrandModal" title="Create New Brand" @close="showBrandModal = false">
        <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
            <input type="text" v-model="newBrandName" @keydown.enter="createBrand" class="w-full px-3 py-2 border-2 border-gray-300 rounded-md">
            <p v-if="brandError" class="text-red-500 text-xs mt-1">{{ brandError }}</p>
        </div>
        <div class="mt-4 flex justify-end gap-2">
             <button @click="showBrandModal = false" class="px-4 py-2 bg-gray-200 rounded text-gray-700">Cancel</button>
             <button @click="createBrand" class="px-4 py-2 bg-indigo-600 text-white rounded">Create</button>
        </div>
    </CommonModal>

    <!-- Barcode Scanner Modal -->
    <CommonModal :is-open="showScannerModal" title="Scan Barcode" @close="stopBarcodeScanner" max-width="max-w-2xl">
        <div class="relative bg-black rounded-lg overflow-hidden aspect-video mb-4">
             <video ref="scannerVideoElement" class="w-full h-full object-contain"></video>
             <div class="absolute inset-0 border-2 border-red-500 opacity-50 pointer-events-none" style="top: 20%; bottom: 20%; left: 10%; right: 10%;"></div>
        </div>
        
        <div class="mb-4" v-if="videoDevices.length > 1">
            <label class="block text-sm font-medium text-gray-700 mb-1">Select Camera</label>
            <select v-model="selectedDeviceId" @change="startBarcodeScanner" class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg">
                <option v-for="device in videoDevices" :key="device.deviceId" :value="device.deviceId">
                    {{ device.label || 'Camera ' + (videoDevices.indexOf(device) + 1) }}
                </option>
            </select>
        </div>

        <div class="flex justify-center">
            <button @click="stopBarcodeScanner" class="px-6 py-2 bg-gray-600 text-white rounded-full">
                Cancel
            </button>
        </div>
    </CommonModal>

    <!-- Camera Modal -->
    <CommonModal :show="showCameraModal" title="Capture Image" @close="closeCamera" max-width="max-w-2xl">
        <div class="relative bg-black rounded-lg overflow-hidden aspect-video mb-4">
             <video ref="videoElement" class="w-full h-full object-contain" autoplay playsinline></video>
             <canvas ref="canvasElement" class="hidden"></canvas>
             <img v-if="capturedImage" :src="capturedImage" class="absolute inset-0 w-full h-full object-contain bg-black">
        </div>
        <div class="flex justify-center gap-4">
            <button v-if="!capturedImage" @click="captureImage" class="px-6 py-2 bg-indigo-600 text-white rounded-full">
                 <i class="fas fa-camera mr-2"></i> Capture
            </button>
            <template v-else>
                <button @click="retakeImage" class="px-6 py-2 bg-gray-600 text-white rounded-full">
                    <i class="fas fa-redo mr-2"></i> Retake
                </button>
                <button @click="saveImage" class="px-6 py-2 bg-green-600 text-white rounded-full">
                     <i class="fas fa-check mr-2"></i> Use Photo
                </button>
            </template>
        </div>
    </CommonModal>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed, nextTick } from 'vue';
import CommonModal from '../common/CommonModal.vue'; 
import { BrowserMultiFormatReader, NotFoundException } from '@zxing/library'; 

const props = defineProps({
    isEdit: { type: Boolean, default: false },
    initialProduct: { type: Object, default: () => ({}) },
    oldInput: { type: Object, default: () => ({}) },
    errors: { type: Object, default: () => ({}) },
    categories: { type: Array, default: () => [] },
    brands: { type: Array, default: () => [] },
    warehouses: { type: Array, default: () => [] },
    existingImages: { type: Array, default: () => [] },
    actionUrl: { type: String, required: true },
    csrfToken: { type: String, required: true },
    routes: { type: Object, required: true }
});

const localCategories = ref([...props.categories]);
const localBrands = ref([...props.brands]);

const getInitialStock = (warehouseId) => {
    if (props.oldInput.warehouse_stock && props.oldInput.warehouse_stock[warehouseId]) {
        return props.oldInput.warehouse_stock[warehouseId];
    }
    if (props.initialProduct.warehouses) {
        const wh = props.initialProduct.warehouses.find(w => w.id === warehouseId);
        return wh ? wh.pivot.quantity : 0;
    }
    return 0;
};

// Initialize Warehouse Stock Map
const initialStockMap = {};
props.warehouses.forEach(w => {
    initialStockMap[w.id] = getInitialStock(w.id);
});

// Initialize form data with old input or initial product data
const form = reactive({
    name: props.oldInput.name || props.initialProduct.name || '',
    category_id: props.oldInput.category_id || props.initialProduct.category_id || '',
    brand_id: props.oldInput.brand_id || props.initialProduct.brand_id || '',
    sku: props.oldInput.sku || props.initialProduct.sku || '',
    price: props.oldInput.price || props.initialProduct.price || '',
    compare_at_price: props.oldInput.compare_at_price || props.initialProduct.compare_at_price || '',
    cost_price: props.oldInput.cost_price || props.initialProduct.cost_price || '',
    barcode: props.oldInput.barcode || props.initialProduct.barcode || '',
    flash_sale_price: props.oldInput.flash_sale_price || props.initialProduct.flash_sale_price || '',
    flash_sale_end_date: props.oldInput.flash_sale_end_date || (props.initialProduct.flash_sale_end_date ? props.initialProduct.flash_sale_end_date.substring(0, 16) : '') || '',
    short_description: props.oldInput.short_description || props.initialProduct.short_description || '',
    description: props.oldInput.description || props.initialProduct.description || '',
    track_inventory: props.oldInput.track_inventory ? true : (props.initialProduct.track_inventory !== undefined ? !!props.initialProduct.track_inventory : false),
    stock_quantity: props.oldInput.stock_quantity ?? props.initialProduct.stock_quantity ?? 0,
    warehouse_stock: initialStockMap,
    low_stock_threshold: props.oldInput.low_stock_threshold ?? props.initialProduct.low_stock_threshold ?? 10,
    expiry_date: props.oldInput.expiry_date || (props.initialProduct.expiry_date ? props.initialProduct.expiry_date.substring(0, 10) : '') || '',
    is_active: props.oldInput.is_active !== undefined ? !!props.oldInput.is_active : (props.initialProduct.is_active !== undefined ? !!props.initialProduct.is_active : true),
    is_featured: props.oldInput.is_featured !== undefined ? !!props.oldInput.is_featured : !!props.initialProduct.is_featured,
});

// Initialize profit margin if cost and price exist
if (form.cost_price && form.price && parseFloat(form.cost_price) > 0) {
    profit_margin.value = (((parseFloat(form.price) - parseFloat(form.cost_price)) / parseFloat(form.cost_price)) * 100).toFixed(2);
}

const calculatedTotalStock = computed(() => {
    return Object.values(form.warehouse_stock).reduce((sum, qty) => sum + (parseInt(qty) || 0), 0);
});

const showCategoryModal = ref(false);
const newCategoryName = ref('');
const categoryError = ref('');

const showBrandModal = ref(false);
const newBrandName = ref('');
const brandError = ref('');

const profit_margin = ref('');

const calculatePrice = () => {
    if (form.cost_price && profit_margin.value) {
        const cost = parseFloat(form.cost_price);
        const margin = parseFloat(profit_margin.value);
        if (!isNaN(cost) && !isNaN(margin)) {
            form.price = (cost + (cost * (margin / 100))).toFixed(2);
        }
    }
};

// Barcode Scanner Logic
const showScannerModal = ref(false);
const scannerVideoElement = ref(null);
const videoDevices = ref([]);
const selectedDeviceId = ref(null);
let codeReader = null;

const startBarcodeScanner = async () => {
    showScannerModal.value = true;
    await nextTick();
    
    // Initialize Reader
    if (!codeReader) {
        codeReader = new BrowserMultiFormatReader();
    }
    
    try {
        // Start decoding immediately with currently selected (or default) device
        // This ensures camera permission is requested immediately by the browser
        await codeReader.decodeFromVideoDevice(selectedDeviceId.value, scannerVideoElement.value, (result, err) => {
            if (result) {
                form.barcode = result.text;
                stopBarcodeScanner();
            }
            if (err && !(err instanceof NotFoundException)) {
                console.error(err);
            }
        });

        // Then populate device list for swtiching
        const devices = await codeReader.listVideoInputDevices();
        videoDevices.value = devices;
        
        // If we don't have a selected device yet, try to infer the active one or default to the first
        if (!selectedDeviceId.value && devices.length > 0) {
            // Prefer back camera if we can find it
            const backCamera = devices.find(d => d.label.toLowerCase().includes('back') || d.label.toLowerCase().includes('environment'));
            selectedDeviceId.value = backCamera ? backCamera.deviceId : devices[0].deviceId;
        }

    } catch (err) {
        console.error(err);
        alert("Error starting scanner: " + err.message);
        showScannerModal.value = false;
    }
};

const stopBarcodeScanner = () => {
    if (codeReader) {
        codeReader.reset();
    }
    showScannerModal.value = false;
};

const showCameraModal = ref(false);
const videoElement = ref(null);
const canvasElement = ref(null);
const imageInput = ref(null);
const capturedImage = ref(null);
let stream = null;

const errorClass = (field) => {
    return props.errors[field] ? 'border-red-500' : 'border-gray-300';
};

const createCategory = async () => {
    if (!newCategoryName.value.trim()) {
        categoryError.value = 'Name is required';
        return;
    }
    categoryError.value = '';

    try {
        const response = await fetch(props.routes.storeCategory, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': props.csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ name: newCategoryName.value, is_active: true })
        });
        const data = await response.json();
        
        if (data.success) {
            localCategories.value.push(data.category);
            form.category_id = data.category.id;
            // Native select update via v-model
            showCategoryModal.value = false;
            newCategoryName.value = '';
        } else {
            categoryError.value = data.message || 'Error';
        }
    } catch (e) {
        categoryError.value = 'Error occurred';
    }
};

const createBrand = async () => {
    if (!newBrandName.value.trim()) {
        brandError.value = 'Name is required';
        return;
    }
    brandError.value = '';

    try {
        const response = await fetch(props.routes.storeBrand, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': props.csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ name: newBrandName.value, is_active: true })
        });
        const data = await response.json();
        
        if (data.success) {
            localBrands.value.push(data.brand);
            form.brand_id = data.brand.id;
            showBrandModal.value = false;
            newBrandName.value = '';
        } else {
            brandError.value = data.message || 'Error';
        }
    } catch (e) {
        brandError.value = 'Error occurred';
    }
};

const openCamera = async () => {
    showCameraModal.value = true;
    capturedImage.value = null;
    try {
        stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
        // Use nextTick or simple timeout to ensure video element is rendered
        setTimeout(() => {
             if (videoElement.value) videoElement.value.srcObject = stream;
        }, 100);
    } catch (err) {
        alert("Could not access camera.");
        showCameraModal.value = false;
    }
};

const closeCamera = () => {
    if (stream) {
        stream.getTracks().forEach(track => track.stop());
    }
    showCameraModal.value = false;
    stream = null;
};

const captureImage = () => {
    const video = videoElement.value;
    const canvas = canvasElement.value;
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    const context = canvas.getContext('2d');
    context.drawImage(video, 0, 0, canvas.width, canvas.height);
    capturedImage.value = canvas.toDataURL('image/jpeg');
};

const retakeImage = () => {
    capturedImage.value = null;
};

const saveImage = () => {
    if (!capturedImage.value) return;

    const byteString = atob(capturedImage.value.split(',')[1]);
    const mimeString = capturedImage.value.split(',')[0].split(':')[1].split(';')[0];
    const ab = new ArrayBuffer(byteString.length);
    const ia = new Uint8Array(ab);
    for (let i = 0; i < byteString.length; i++) {
        ia[i] = byteString.charCodeAt(i);
    }
    const blob = new Blob([ab], {type: mimeString});
    const file = new File([blob], "camera-capture-" + Date.now() + ".jpg", {type: mimeString});

    const dataTransfer = new DataTransfer();
    
    // Append to existing files if any?
    // Vue input ref
    if (imageInput.value.files) {
        Array.from(imageInput.value.files).forEach(f => dataTransfer.items.add(f));
    }
    dataTransfer.items.add(file);
    imageInput.value.files = dataTransfer.files;

    closeCamera();
};
</script>
