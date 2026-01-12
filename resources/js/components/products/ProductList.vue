<template>
  <div class="content-wrapper">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4 md:mb-6 gap-3">
      <h2 class="text-xl md:text-2xl font-bold text-gray-800">Products</h2>
      <div class="flex gap-2 w-full md:w-auto">
        <div class="relative">
          <button @click="openExport = !openExport" v-click-outside="() => openExport = false" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg transition border-2 border-gray-300 flex items-center gap-2">
            <i class="fas fa-file-export"></i> Export / Import
            <i class="fas fa-chevron-down text-xs ml-1"></i>
          </button>
          <div v-show="openExport" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 border border-gray-100">
            <a :href="routes.export" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
              <i class="fas fa-download mr-2"></i> Export CSV
            </a>
            <a :href="routes.template" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
              <i class="fas fa-file-csv mr-2"></i> Download Template
            </a>
            <button @click="showImport = true; openExport = false" class="w-full text-left block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
              <i class="fas fa-file-upload mr-2"></i> Import CSV
            </button>
            <a :href="routes.bulkUpload" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
              <i class="fas fa-images mr-2"></i> Bulk Upload Images
            </a>
          </div>
        </div>
        <a :href="routes.create" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 md:px-6 py-2 rounded-lg transition flex-1 md:flex-none text-center">
          + Add Product
        </a>
      </div>
    </div>

    <!-- Import Modal -->
    <div v-if="showImport" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showImport = false" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form :action="routes.import" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="_token" :value="csrfToken">
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
                                        <input type="file" name="file" accept=".csv,.txt,.zip" required class="w-full border-2 border-gray-300 p-2 rounded bg-gray-50 text-sm">
                                    </div>
                                    <div class="mt-4 text-xs text-gray-400">
                                        <a :href="routes.template" class="text-blue-600 hover:underline">Don't have a template? Download here.</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                            Import Data
                        </button>
                        <button type="button" @click="showImport = false" class="mt-3 w-full inline-flex justify-center rounded-md border-2 border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Filters (Passed as Slot or Retained) -->
    <div class="bg-white rounded-lg shadow p-3 md:p-6 mb-4 md:mb-6">
        <slot name="filters"></slot>
    </div>

    <!-- Bulk Actions -->
    <div class="bg-white rounded-lg shadow p-2 md:p-4 mb-3 md:mb-4">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-2">
            <div class="flex items-center gap-2 md:gap-4 w-full md:w-auto">
                <span class="text-xs md:text-sm text-gray-600">
                    {{ selectedProducts.length }} selected
                </span>
                
                <div class="flex gap-2">
                    <select v-model="bulkAction" class="border-2 border-gray-300 rounded-lg px-2 md:px-4 py-1.5 md:py-2 text-xs md:text-sm focus:ring-2 focus:ring-indigo-500">
                        <option value="">Bulk Actions</option>
                        <option value="mark_featured">⭐ Mark as Featured</option>
                        <option value="unmark_featured">Remove Featured</option>
                        <option value="enable_flash_sale">🔥 Enable Flash Sale</option>
                        <option value="disable_flash_sale">Disable Flash Sale</option>
                        <option value="activate">✅ Activate</option>
                        <option value="deactivate">❌ Deactivate</option>
                        <option value="delete">🗑️ Delete</option>
                    </select>
                    
                    <button type="button" @click="handleBulkAction" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-1.5 md:py-2 rounded-lg text-xs md:text-sm transition">
                        Apply
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Flash Sale Modal -->
    <div v-if="showFlashSaleModal" class="fixed inset-0 z-50 overflow-y-auto" @click="showFlashSaleModal = false">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75"></div>
            <div class="relative bg-white rounded-lg p-6 max-w-md w-full" @click.stop>
                <h3 class="text-lg font-bold mb-4">Set Flash Sale</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Flash Sale Price</label>
                        <input type="number" v-model="flashSale.price" step="0.01" class="w-full border-2 border-gray-300 rounded-lg px-4 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Start Date & Time</label>
                        <input type="datetime-local" v-model="flashSale.start" class="w-full border-2 border-gray-300 rounded-lg px-4 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">End Date & Time</label>
                        <input type="datetime-local" v-model="flashSale.end" class="w-full border-2 border-gray-300 rounded-lg px-4 py-2">
                    </div>
                    <div class="flex gap-2 pt-4">
                        <button @click="submitFlashSale" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg">
                            Apply Flash Sale
                        </button>
                        <button @click="showFlashSaleModal = false" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg">
                            Cancel
                        </button>
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
                        <input type="checkbox" :checked="selectAll" @change="toggleAll" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Serial Number</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Expiry</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="product in products.data" :key="product.id">
                    <td class="px-6 py-4">
                        <input type="checkbox" :value="product.id" v-model="selectedProducts" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <ProductImageUpload 
                              :product-id="product.id" 
                    :initial-image-url="product.image_url"
                              :product-name="product.name"
                              :upload-url="`/admin/products/${product.id}/quick-image`"
                              :csrf-token="csrfToken"
                              :tenant-slug="tenantSlug" 
                              class="mr-3"
                            />
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ product.name }}</div>
                                <span v-if="product.is_featured" class="text-xs text-yellow-600">⭐ Featured</span>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ product.sku }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ product.category?.name || '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                        {{ currencySymbol }}{{ formatNumber(product.price) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span v-if="product.track_inventory" :class="['text-sm', isLowStock(product) ? 'text-red-600 font-bold' : 'text-gray-900']">
                            {{ product.stock_quantity }}
                        </span>
                        <span v-else class="text-sm text-gray-400">N/A</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ formatDate(product.expiry_date) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span v-if="product.is_active" class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                        <span v-else class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Inactive</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a :href="`${routes.base}/${product.id}/edit`" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                        <button type="button" @click="deleteProduct(product.id)" class="text-red-600 hover:text-red-900">Delete</button>
                    </td>
                </tr>
                <tr v-if="products.data.length === 0">
                    <td colspan="9" class="px-6 py-12 text-center text-gray-500">
                        No products found. <a :href="routes.create" class="text-indigo-600 hover:underline">Create your first product</a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Products Cards (Mobile) -->
    <div class="lg:hidden space-y-3">
        <div v-for="product in products.data" :key="product.id" class="bg-white rounded-lg shadow p-3">
            <div class="flex items-start justify-between mb-2">
                <div class="flex items-center gap-2">
                    <input type="checkbox" :value="product.id" v-model="selectedProducts" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <h3 class="text-sm font-semibold text-gray-900 line-clamp-2">{{ product.name }}</h3>
                </div>
                <span v-if="product.is_featured" class="text-lg">⭐</span>
            </div>
            
            <div class="flex gap-3">
                <ProductImageUpload 
                    :product-id="product.id" 
                    :initial-image-url="product.image_url"
                    :product-name="product.name"
                    :upload-url="`/admin/products/${product.id}/quick-image`"
                    :csrf-token="csrfToken"
                    :tenant-slug="tenantSlug"
                />
                
                <div class="flex-1 min-w-0 space-y-1">
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-gray-500">S/N:</span>
                        <span class="text-xs font-medium text-gray-900">{{ product.sku }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-gray-500">Category:</span>
                        <span class="text-xs text-gray-700">{{ product.category?.name || '-' }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-gray-500">Price:</span>
                        <span class="text-sm font-bold text-gray-900">{{ currencySymbol }}{{ formatNumber(product.price) }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-gray-500">Stock:</span>
                        <span v-if="product.track_inventory" :class="['text-xs', isLowStock(product) ? 'text-red-600 font-bold' : 'text-gray-900']">
                            {{ product.stock_quantity }} units
                        </span>
                        <span v-else class="text-xs text-gray-400">N/A</span>
                    </div>
                </div>
            </div>
            
            <div class="flex gap-2 mt-3 pt-3 border-t border-gray-200">
                <a :href="`${routes.base}/${product.id}/edit`" class="flex-1 text-center bg-indigo-50 hover:bg-indigo-100 text-indigo-600 px-3 py-2 rounded text-sm font-medium transition">
                    <i class="fas fa-edit mr-1"></i> Edit
                </a>
                <button type="button" @click="deleteProduct(product.id)" class="flex-1 text-center bg-red-50 hover:bg-red-100 text-red-600 px-3 py-2 rounded text-sm font-medium transition">
                    <i class="fas fa-trash mr-1"></i> Delete
                </button>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        <slot name="pagination"></slot>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, watch } from 'vue';
import ProductImageUpload from './ProductImageUpload.vue';

const props = defineProps({
  products: {
    type: Object,
    required: true
  },
  currencySymbol: String,
  csrfToken: String,
  tenantSlug: String,
  routes: {
    type: Object,
    required: true
  }
});

const openExport = ref(false);
const showImport = ref(false);
const bulkAction = ref('');
const selectedProducts = ref([]);
const showFlashSaleModal = ref(false);

const flashSale = reactive({
  price: '',
  start: '',
  end: ''
});

const selectAll = computed({
  get: () => selectedProducts.value.length === props.products.data.length && props.products.data.length > 0,
  set: (val) => {
    if (val) {
      selectedProducts.value = props.products.data.map(p => p.id);
    } else {
      selectedProducts.value = [];
    }
  }
});

const toggleAll = () => {
    // handled by computed
};

const formatNumber = (num) => {
  return parseFloat(num).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
};

const formatDate = (dateString) => {
    if(!dateString) return '-';
    return new Date(dateString).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
};

const isLowStock = (product) => {
    return product.stock_quantity <= (product.low_stock_threshold || 10);
};

const handleBulkAction = () => {
    if (!bulkAction.value) {
        alert('Please select an action first');
        return;
    }
    
    if (selectedProducts.value.length === 0) {
        alert('Please select at least one product');
        return;
    }

    if (bulkAction.value === 'enable_flash_sale') {
        showFlashSaleModal.value = true;
        return;
    }

    if (bulkAction.value === 'delete' && !confirm(`Are you sure you want to delete ${selectedProducts.value.length} products?`)) {
        return;
    }

    submitBulkForm(bulkAction.value);
};

const submitFlashSale = () => {
    if (!flashSale.price || !flashSale.start || !flashSale.end) {
        alert('Please fill all flash sale fields');
        return;
    }
    submitBulkForm('enable_flash_sale', {
        flash_sale_price: flashSale.price,
        flash_sale_start: flashSale.start,
        flash_sale_end: flashSale.end
    });
};

const submitBulkForm = (action, extraData = {}) => {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = props.routes.bulkAction;

    const addInput = (name, value) => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        input.value = value;
        form.appendChild(input);
    };

    addInput('_token', props.csrfToken);
    addInput('action', action);

    for (const [key, value] of Object.entries(extraData)) {
        addInput(key, value);
    }

    selectedProducts.value.forEach(id => {
        addInput('product_ids[]', id);
    });

    document.body.appendChild(form);
    form.submit();
};

const deleteProduct = (id) => {
    if (!confirm('Are you sure you want to delete this product?')) return;
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `${props.routes.base}/${id}`;
    
    const addInput = (name, value) => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        input.value = value;
        form.appendChild(input);
    };

    addInput('_token', props.csrfToken);
    addInput('_method', 'DELETE');

    document.body.appendChild(form);
    form.submit();
};

// Directive for click outside
const vClickOutside = {
  mounted(el, binding) {
    el.clickOutsideEvent = function(event) {
      if (!(el === event.target || el.contains(event.target))) {
        binding.value(event, el);
      }
    };
    document.body.addEventListener('click', el.clickOutsideEvent);
  },
  unmounted(el) {
    document.body.removeEventListener('click', el.clickOutsideEvent);
  }
};
</script>
