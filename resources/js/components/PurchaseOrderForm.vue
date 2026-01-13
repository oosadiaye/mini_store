<template>
<div class="purchase-order-form-container">
    <div v-if="error" class="mb-6 p-5 bg-red-50 border-l-4 border-red-500 rounded-xl flex items-center justify-between shadow-sm">
        <div class="flex items-center">
            <svg class="w-6 h-6 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div class="text-red-700 font-bold" v-text="error"></div>
        </div>
        <button @click="error = null" class="text-red-500 hover:text-red-700 font-black text-2xl" type="button">&times;</button>
    </div>

    <form @submit.prevent="handleSubmit" class="space-y-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 bg-white p-8 rounded-3xl border border-gray-100 shadow-sm">
            <div class="space-y-2">
                <label class="block text-sm font-bold text-gray-500 uppercase tracking-widest">Supplier</label>
                <div class="flex gap-2">
                    <select v-model="form.supplier_id" required class="flex-1 rounded-2xl border-2 border-gray-100 bg-gray-50/30 px-4 py-3 text-base font-bold text-gray-800 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 outline-none">
                        <option value="">Select Supplier</option>
                        <option v-for="supplier in localSuppliers" :key="supplier.id" :value="supplier.id" v-text="supplier.name"></option>
                    </select>
                    <button type="button" @click.stop="openSupplierModal" class="relative z-10 bg-gray-100 px-4 rounded-2xl border-2 border-gray-100 hover:bg-indigo-50 hover:border-indigo-100 transition-colors text-gray-600 cursor-pointer flex-shrink-0">
                        <svg class="w-6 h-6 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    </button>
                </div>
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-bold text-gray-500 uppercase tracking-widest">Destination Warehouse</label>
                <select v-model="form.warehouse_id" required class="w-full rounded-2xl border-2 border-gray-100 bg-gray-50/30 px-4 py-3 text-base font-bold text-gray-800 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 outline-none">
                    <option value="">Select Warehouse</option>
                    <option v-for="warehouse in warehouses" :key="warehouse.id" :value="warehouse.id" v-text="warehouse.name"></option>
                </select>
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-bold text-gray-500 uppercase tracking-widest">Order Date</label>
                <input type="date" v-model="form.order_date" required class="w-full rounded-2xl border-2 border-gray-100 bg-gray-50/30 px-4 py-3 text-base font-bold text-gray-800 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 outline-none">
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-bold text-gray-500 uppercase tracking-widest">Expected Delivery</label>
                <input type="date" v-model="form.expected_delivery_date" class="w-full rounded-2xl border-2 border-gray-100 bg-gray-50/30 px-4 py-3 text-base font-bold text-gray-800 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 outline-none">
            </div>
        </div>

        <div class="space-y-2">
            <label class="block text-sm font-bold text-gray-500 uppercase tracking-widest">Notes</label>
            <textarea v-model="form.notes" rows="3" class="w-full rounded-2xl border-2 border-gray-100 bg-gray-50/30 px-6 py-4 text-base font-medium text-gray-800 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 outline-none" placeholder="Enter instructions..."></textarea>
        </div>

        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                <h4 class="text-xl font-black text-gray-900">Order Items</h4>
                <button type="button" @click="addItem" class="bg-indigo-600 text-white px-5 py-2.5 rounded-xl font-bold hover:bg-indigo-700 shadow-lg transition-all text-sm">
                    Add Item
                </button>
            </div>
            
            <div class="p-8 space-y-6">
                <div v-for="(item, index) in form.items" :key="index" class="grid grid-cols-12 gap-5 items-end bg-gray-50/30 p-6 rounded-3xl border-2 border-gray-100 transition-all duration-300">
                    <div class="col-span-12 lg:col-span-4 space-y-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">Product</label>
                        <select v-model="item.product_id" @change="handleProductChange(index)" required class="w-full rounded-xl border-2 border-gray-100 bg-white px-3 py-2.5 text-sm font-bold text-gray-800 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 outline-none">
                            <option value="">Select Product</option>
                            <option v-for="product in products" :key="product.id" :value="product.id" v-text="product.name"></option>
                        </select>
                    </div>
                    
                    <div class="col-span-4 lg:col-span-2 space-y-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">Quantity</label>
                        <input type="number" v-model.number="item.quantity" required class="w-full rounded-xl border-2 border-gray-100 bg-white px-3 py-2.5 text-sm font-black text-center text-gray-800 outline-none" min="1">
                    </div>

                    <div class="col-span-4 lg:col-span-2 space-y-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">Unit Cost</label>
                        <input type="number" v-model.number="item.unit_cost" step="0.01" required class="w-full rounded-xl border-2 border-gray-100 bg-white px-3 py-2.5 text-sm font-black text-right text-indigo-600 outline-none" min="0">
                    </div>
                    
                    <div class="col-span-4 lg:col-span-2 space-y-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">Tax Code</label>
                        <select v-model="item.tax_code_id" class="w-full rounded-xl border-2 border-gray-100 bg-white px-3 py-2.5 text-sm font-bold text-gray-800 outline-none">
                            <option value="">No Tax</option>
                            <option v-for="tax in taxCodes" :key="tax.id" :value="tax.id" v-text="tax.name + ' (' + tax.rate + '%)'"></option>
                        </select>
                    </div>

                    <div class="col-span-12 lg:col-span-2 flex items-center justify-end gap-3">
                        <div class="text-right">
                             <div class="text-base font-black text-gray-900" v-text="formatCurrency(calculateRowTotal(item))"></div>
                        </div>
                        <button type="button" @click="removeItem(index)" :disabled="form.items.length === 1" class="text-red-400 hover:text-red-500 hover:bg-red-50 p-2 rounded-xl transition-all disabled:opacity-0" title="Remove">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="flex justify-end pt-4">
            <div class="w-full md:w-80 bg-white rounded-3xl p-8 border-2 border-indigo-50 space-y-4 shadow-sm">
                <div class="flex justify-between items-center text-sm font-bold text-gray-500">
                    <span>Subtotal</span>
                    <span class="text-gray-800" v-text="formatCurrency(totals.subtotal)"></span>
                </div>
                
                <div class="flex justify-between items-center text-sm font-bold text-gray-500">
                    <span>Discount</span>
                     <input type="number" v-model.number="form.discount" step="0.01" class="w-24 bg-gray-50 rounded-lg text-right text-xs font-black px-2 py-1 outline-none">
                </div>
                
                <div class="flex justify-between items-center text-sm font-bold text-gray-500">
                     <span>Tax</span>
                     <span class="text-gray-800" v-text="formatCurrency(totals.tax)"></span>
                </div>
                
                <div class="flex justify-between items-center text-sm font-bold text-gray-500">
                     <span>Shipping</span>
                     <input type="number" v-model.number="form.shipping" step="0.01" class="w-24 bg-gray-50 rounded-lg text-right text-xs font-black px-2 py-1 outline-none">
                </div>

                <div class="pt-4 border-t-2 border-indigo-50 flex justify-between items-center">
                    <span class="text-sm font-black text-gray-900 uppercase tracking-widest">Total</span>
                    <div class="text-2xl font-black text-indigo-600" v-text="formatCurrency(totals.total)"></div>
                </div>
            </div>
        </div>

        <div class="pt-8 flex justify-end">
             <button type="submit" :disabled="isSubmitting" class="bg-indigo-600 text-white px-12 py-5 rounded-2xl font-black text-xl hover:bg-indigo-700 shadow-xl transition-all disabled:opacity-50">
                <span v-text="isSubmitting ? 'Saving...' : (initialData ? 'Update Order' : 'Place Order')"></span>
            </button>
        </div>
    </form>

    <!-- Supplier Modal -->
    <CommonModal :is-open="showSupplierModal" title="Quick Add Supplier" @close="showSupplierModal = false">
        <div class="bg-white px-8 pt-8 pb-8 sm:p-10 sm:pb-8">
            <h3 class="text-2xl font-black text-gray-900 mb-6">Quick Add Supplier</h3>
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-bold text-gray-500 uppercase tracking-widest mb-1">Supplier Name</label>
                    <input type="text" v-model="newSupplier.name" class="w-full rounded-xl border-2 border-gray-100 px-4 py-2 outline-none focus:border-indigo-500">
                    <p v-if="supplierError" class="text-red-500 text-xs mt-1">{{ supplierError }}</p>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-500 uppercase tracking-widest mb-1">Email (Optional)</label>
                    <input type="email" v-model="newSupplier.email" class="w-full rounded-xl border-2 border-gray-100 px-4 py-2 outline-none focus:border-indigo-500">
                </div>
                 <div>
                    <label class="block text-sm font-bold text-gray-500 uppercase tracking-widest mb-1">Phone (Optional)</label>
                    <input type="tel" v-model="newSupplier.phone" class="w-full rounded-xl border-2 border-gray-100 px-4 py-2 outline-none focus:border-indigo-500">
                </div>
            </div>
        </div>
        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
             <button @click="createSupplier" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">Create Supplier</button>
             <button @click="showSupplierModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Cancel</button>
        </div>
    </CommonModal>
</div>
</template>

<script>
import axios from 'axios';
import CommonModal from './common/CommonModal.vue';

export default {
    name: 'PurchaseOrderForm',
    props: {
        suppliers: { type: Array, required: true },
        warehouses: { type: Array, required: true },
        products: { type: Array, required: true },
        taxCodes: { type: Array, required: true },
        initialData: { type: Object, default: null },
        submitUrl: { type: String, required: true },
        redirectUrl: { type: String, required: true },
        method: { type: String, default: 'POST' },
        currency: { type: String, default: '₦' },
        supplierStoreUrl: { type: String, default: '' },
    },
    components: {
        CommonModal
    },
    data() {
        return {
            isSubmitting: false,
            localSuppliers: [...this.suppliers],
            showSupplierModal: false,
            newSupplier: { name: '', email: '', phone: '' },
            supplierError: '',
            error: null,
            form: {
                supplier_id: '',
                warehouse_id: '',
                order_date: new Date().toISOString().substr(0, 10),
                expected_delivery_date: '',
                notes: '',
                discount: 0,
                shipping: 0,
                items: [
                    { product_id: '', quantity: 1, unit_cost: 0, tax_code_id: '' }
                ]
            }
        };
    },
    watch: {
        suppliers(newVal) {
            this.localSuppliers = [...newVal];
        }
    },
    mounted() {
        if (this.initialData) {
            Object.assign(this.form, {
                ...this.initialData,
                items: this.initialData.items ? this.initialData.items.map(i => ({
                    id: i.id,
                    product_id: i.product_id,
                    quantity: i.quantity_ordered,
                    unit_cost: parseFloat(i.unit_cost) || 0,
                    tax_code_id: i.tax_code_id || ''
                })) : this.form.items
            });
        }
    },
    methods: {
        openSupplierModal() {
            this.showSupplierModal = true;
        },
        async createSupplier() {
            if (!this.newSupplier.name) {
                this.supplierError = 'Name is required';
                return;
            }
            this.supplierError = '';
            
            try {
                // Assuming CSRF token is in meta tag
                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const res = await axios.post(this.supplierStoreUrl, this.newSupplier, {
                    headers: { 'X-CSRF-TOKEN': token }
                });
                
                if (res.data.success || res.status < 300) {
                     // Check response structure, assume it returns the supplier or we fetch list?
                     // Assuming standard Laravel resource store returns the object on 201
                     // Or returns JSON
                     const createdSupplier = res.data.supplier || res.data;
                     this.localSuppliers.push(createdSupplier);
                     this.form.supplier_id = createdSupplier.id;
                     this.showSupplierModal = false;
                     this.newSupplier = { name: '', email: '', phone: '' };
                }
            } catch (err) {
                 this.supplierError = err.response?.data?.message || 'Failed to create supplier';
            }
        },
        handleProductChange(index) {
            const item = this.form.items[index];
            const product = this.products.find(p => p.id === item.product_id);
            if (product) {
                item.unit_cost = parseFloat(product.cost_price) || 0;
            }
        },
        addItem() {
            this.form.items.push({ product_id: '', quantity: 1, unit_cost: 0, tax_code_id: '' });
        },
        removeItem(index) {
            if (this.form.items.length > 1) {
                this.form.items.splice(index, 1);
            }
        },
        calculateRowTotal(item) {
            const qty = parseFloat(item.quantity) || 0;
            const cost = parseFloat(item.unit_cost) || 0;
            const subtotal = qty * cost;
            const taxRate = this.getTaxRate(item.tax_code_id);
            return subtotal + (subtotal * (taxRate / 100));
        },
        getTaxRate(taxId) {
            const tax = this.taxCodes.find(t => t.id === taxId);
            return tax ? parseFloat(tax.rate) : 0;
        },
        formatCurrency(value) {
            return this.currency + (parseFloat(value) || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },
        async handleSubmit() {
            this.isSubmitting = true;
            this.error = null;
            try {
                const payload = { ...this.form };
                if (this.method === 'PUT') payload._method = 'PUT';
                const res = await axios.post(this.submitUrl, payload);
                if (res.status < 300) window.location.href = this.redirectUrl;
                else throw new Error(res.data.message || 'Error');
            } catch (err) {
                this.error = err.response?.data?.message || 'Check form inputs and try again.';
            } finally {
                this.isSubmitting = false;
            }
        }
    },
    computed: {
        totals() {
            let subtotal = 0;
            let tax = 0;
            this.form.items.forEach(item => {
                const rs = (parseFloat(item.quantity) || 0) * (parseFloat(item.unit_cost) || 0);
                subtotal += rs;
                tax += rs * (this.getTaxRate(item.tax_code_id) / 100);
            });
            const total = Math.max(0, subtotal - (parseFloat(this.form.discount) || 0) + tax + (parseFloat(this.form.shipping) || 0));
            return { subtotal, tax, total };
        }
    }
};
</script>

<style>
/* Reset for Vue SFC parsing safety */
.purchase-order-form-container select {
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%239ca3af'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 1rem center;
    background-size: 1.25rem;
}
</style>
