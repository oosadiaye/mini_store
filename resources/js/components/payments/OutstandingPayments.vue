<template>
  <div>
    <!-- Tabs -->
    <div class="flex flex-col md:flex-row space-y-3 md:space-y-0 md:space-x-3 mb-8">
      <button 
        type="button"
        @click="activeTab = 'receivables'" 
        :class="activeTab === 'receivables' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-100 ring-2 ring-indigo-600 ring-offset-2' : 'bg-white text-gray-600 hover:bg-gray-50 border border-gray-200'"
        class="whitespace-nowrap py-2.5 px-6 rounded-xl font-bold text-sm transition-all duration-300 flex items-center"
      >
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
        Receivables
        <span v-if="receivablesTotal > 0" :class="activeTab === 'receivables' ? 'bg-white text-indigo-600' : 'bg-indigo-100 text-indigo-600'" class="ml-2 py-0.5 px-2.5 rounded-full text-xs transition-colors duration-300">
          {{ receivablesTotal }}
        </span>
      </button>
      <button 
        type="button"
        @click="activeTab = 'payables'" 
        :class="activeTab === 'payables' ? 'bg-red-600 text-white shadow-lg shadow-red-100 ring-2 ring-red-600 ring-offset-2' : 'bg-white text-gray-600 hover:bg-gray-50 border border-gray-200'"
        class="whitespace-nowrap py-2.5 px-6 rounded-xl font-bold text-sm transition-all duration-300 flex items-center"
      >
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6"></path></svg>
        Payables
        <span v-if="payablesTotal > 0" :class="activeTab === 'payables' ? 'bg-white text-red-600' : 'bg-red-100 text-red-600'" class="ml-2 py-0.5 px-2.5 rounded-full text-xs transition-colors duration-300">
          {{ payablesTotal }}
        </span>
      </button>
      <button 
        type="button"
        @click="activeTab = 'unallocated'" 
        :class="activeTab === 'unallocated' ? 'bg-green-600 text-white shadow-lg shadow-green-100 ring-2 ring-green-600 ring-offset-2' : 'bg-white text-gray-600 hover:bg-gray-50 border border-gray-200'"
        class="whitespace-nowrap py-2.5 px-6 rounded-xl font-bold text-sm transition-all duration-300 flex items-center"
      >
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
        Unallocated
        <span v-if="unallocatedTotal > 0" :class="activeTab === 'unallocated' ? 'bg-white text-green-600' : 'bg-green-100 text-green-600'" class="ml-2 py-0.5 px-2.5 rounded-full text-xs transition-colors duration-300">
          {{ unallocatedTotal }}
        </span>
      </button>

      <div class="hidden md:block flex-grow"></div>
      
      <button 
        @click="unallocatedModal.open = true"
        class="w-full md:w-auto bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl font-bold text-sm transition-all duration-300 flex items-center shadow-lg shadow-indigo-100"
      >
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
        Post Advance Payment
      </button>
    </div>

    <!-- Receivables Content -->
    <div v-show="activeTab === 'receivables'">
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden overflow-x-auto text-sm">
        <table class="w-full text-left">
          <thead class="bg-gray-50 text-gray-500 border-b border-gray-100 uppercase text-xs">
            <tr>
              <th class="px-6 py-3">Order #</th>
              <th class="px-6 py-3">Customer</th>
              <th class="px-6 py-3">Date</th>
              <th class="px-6 py-3 text-right">Total</th>
              <th class="px-6 py-3 text-right">Paid</th>
              <th class="px-6 py-3 text-right">Balance Due</th>
              <th class="px-6 py-3 text-right">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="order in receivables" :key="order.id" class="hover:bg-gray-50 transition-colors">
              <td class="px-6 py-4 font-medium text-indigo-600">
                <a :href="getOrderUrl(order.id)">{{ order.order_number }}</a>
              </td>
              <td class="px-6 py-4">{{ order.customer?.name }}</td>
              <td class="px-6 py-4 text-gray-500">{{ formatDate(order.created_at) }}</td>
              <td class="px-6 py-4 text-right font-medium">{{ currencySymbol }}{{ formatNumber(order.total) }}</td>
              <td class="px-6 py-4 text-right text-green-600">{{ currencySymbol }}{{ formatNumber(order.amount_paid) }}</td>
              <td class="px-6 py-4 text-right font-bold text-red-600">{{ currencySymbol }}{{ formatNumber(order.total - order.amount_paid) }}</td>
              <td class="px-6 py-4 text-right">
                <button 
                  @click="openPaymentModal('customer', order.id, order.total - order.amount_paid, order.order_number)" 
                  class="text-indigo-600 hover:text-indigo-900 font-bold uppercase border border-indigo-200 px-3 py-1 rounded-full hover:bg-indigo-50 transition text-[10px]"
                >
                  Record Payment
                </button>
              </td>
            </tr>
            <tr v-if="receivables.length === 0">
              <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                <div class="flex flex-col items-center justify-center text-sm">
                  <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                  <p>No outstanding receivables found.</p>
                  <p class="text-xs mt-1">Great job! All customer orders are paid.</p>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
        <div v-if="receivablesHasPages" class="px-6 py-4 border-t border-gray-200">
           <slot name="receivables-pagination"></slot>
        </div>
      </div>
    </div>

    <!-- Payables Content -->
    <div v-show="activeTab === 'payables'">
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden overflow-x-auto text-sm">
        <table class="w-full text-left">
          <thead class="bg-gray-50 text-gray-500 border-b border-gray-100 uppercase text-xs">
            <tr>
              <th class="px-6 py-3">Invoice #</th>
              <th class="px-6 py-3">Supplier</th>
              <th class="px-6 py-3">Date</th>
              <th class="px-6 py-3 text-right">Total</th>
              <th class="px-6 py-3 text-right">Paid</th>
              <th class="px-6 py-3 text-right">Balance Due</th>
              <th class="px-6 py-3 text-right">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="invoice in payables" :key="invoice.id" class="hover:bg-gray-50 transition-colors">
              <td class="px-6 py-4 font-medium text-gray-900 font-mono text-xs">
                {{ invoice.invoice_number }}
              </td>
              <td class="px-6 py-4">{{ invoice.supplier?.name }}</td>
              <td class="px-6 py-4 text-gray-500">{{ formatDate(invoice.invoice_date) }}</td>
              <td class="px-6 py-4 text-right font-medium">{{ currencySymbol }}{{ formatNumber(invoice.total) }}</td>
              <td class="px-6 py-4 text-right text-green-600">{{ currencySymbol }}{{ formatNumber(invoice.amount_paid) }}</td>
              <td class="px-6 py-4 text-right font-bold text-red-600">{{ currencySymbol }}{{ formatNumber(invoice.total - invoice.amount_paid) }}</td>
              <td class="px-6 py-4 text-right">
                <button 
                  @click="openPaymentModal('supplier', invoice.id, invoice.total - invoice.amount_paid, invoice.invoice_number)" 
                  class="text-red-600 hover:text-red-900 font-bold uppercase border border-red-200 px-3 py-1 rounded-full hover:bg-red-50 transition text-[10px]"
                >
                  Pay Bill
                </button>
              </td>
            </tr>
            <tr v-if="payables.length === 0">
              <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                 <div class="flex flex-col items-center justify-center text-sm">
                  <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                  <p>No outstanding bills found.</p>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
        <div v-if="payablesHasPages" class="px-6 py-4 border-t border-gray-200">
           <slot name="payables-pagination"></slot>
        </div>
      </div>
    </div>

    <!-- Unallocated Content -->
    <div v-show="activeTab === 'unallocated'">
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden overflow-x-auto text-sm">
        <table class="w-full text-left">
          <thead class="bg-gray-50 text-gray-500 border-b border-gray-100 uppercase text-xs">
            <tr>
              <th class="px-6 py-3">Date</th>
              <th class="px-6 py-3">Entity</th>
              <th class="px-6 py-3">Source</th>
              <th class="px-6 py-3 text-right">Total Amount</th>
              <th class="px-6 py-3 text-right">Unallocated</th>
              <th class="px-6 py-3 text-right">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="payment in unallocatedPayments" :key="payment.id" class="hover:bg-gray-50 transition-colors">
              <td class="px-6 py-4 text-gray-500">{{ formatDate(payment.payment_date) }}</td>
              <td class="px-6 py-4 font-medium">{{ payment.entity?.name }}</td>
              <td class="px-6 py-4 text-xs">
                <span :class="payment.entity_type.includes('Customer') ? 'bg-blue-100 text-blue-700' : 'bg-orange-100 text-orange-700'" class="px-2 py-0.5 rounded-full font-semibold">
                  {{ payment.entity_type.includes('Customer') ? 'Customer' : 'Supplier' }}
                </span>
              </td>
              <td class="px-6 py-4 text-right font-medium">{{ currencySymbol }}{{ formatNumber(payment.amount) }}</td>
              <td class="px-6 py-4 text-right font-bold text-green-600">{{ currencySymbol }}{{ formatNumber(payment.unallocated_amount) }}</td>
              <td class="px-6 py-4 text-right">
                <button 
                  @click="openAllocationModal(payment)" 
                  class="text-green-600 hover:text-green-900 font-bold uppercase border border-green-200 px-3 py-1 rounded-full hover:bg-green-50 transition text-[10px]"
                >
                  Allocate
                </button>
              </td>
            </tr>
            <tr v-if="unallocatedPayments.length === 0">
              <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                 <div class="flex flex-col items-center justify-center text-sm">
                  <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm3-2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                  <p>No unallocated payments found.</p>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
        <div v-if="unallocatedHasPages" class="px-6 py-4 border-t border-gray-200">
           <slot name="unallocated-pagination"></slot>
        </div>
      </div>
    </div>

    <!-- Original Payment Modal -->
    <CommonModal :is-open="modal.open" max-width="lg" @close="modal.open = false">
      <form method="POST" :action="getPaymentUrl()" class="w-full">
        <input type="hidden" name="_token" :value="csrfToken">
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
          <div class="sm:flex sm:items-start">
            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
              <svg class="h-6 w-6 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
              <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                Record Payment <span>{{ modal.type === 'customer' ? 'from Customer' : 'to Supplier' }}</span>
              </h3>
              <div class="mt-2 text-sm text-gray-500">
                Record payment for <span class="font-bold font-mono">{{ modal.ref }}</span>.
              </div>
              
              <div class="mt-4 space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700">Amount</label>
                  <div class="mt-1 relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <span class="text-gray-500 sm:text-sm">{{ currencySymbol }}</span>
                    </div>
                    <input type="number" name="amount" step="0.01" v-model="modal.amount" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-7 pr-12 sm:text-sm border-gray-300 rounded-md border-2">
                  </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Payment Date</label>
                    <input type="date" name="payment_date" :value="today" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md border-2">
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Method</label>
                    <select name="payment_method" class="mt-1 block w-full py-2 px-3 border-2 border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                      <option value="bank_transfer">Bank Transfer</option>
                      <option value="cash">Cash</option>
                      <option value="check">Check</option>
                      <option value="credit_card">Credit Card</option>
                    </select>
                  </div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700">Reference / Notes</label>
                  <input type="text" name="reference" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md border-2">
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse rounded-b-lg">
          <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
            Confirm Payment
          </button>
          <button @click="modal.open = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border-2 border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
            Cancel
          </button>
        </div>
      </form>
    </CommonModal>

    <!-- Post Advance Payment Modal -->
    <CommonModal :is-open="unallocatedModal.open" max-width="lg" @close="unallocatedModal.open = false">
      <form method="POST" :action="`/${tenantSlug}/admin/payments/advance`" class="w-full">
        <input type="hidden" name="_token" :value="csrfToken">
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
          <div class="sm:flex sm:items-start">
            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10 text-indigo-600">
               <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            </div>
            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
              <h3 class="text-lg leading-6 font-medium text-gray-900">Post Advance Payment</h3>
              <div class="mt-2 text-sm text-gray-500">Record a payment that isn't yet tied to a specific order or bill.</div>
              
              <div class="mt-4 space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700">Source Type</label>
                  <div class="mt-1 flex space-x-2">
                    <button type="button" @click="unallocatedModal.type = 'customer'" :class="unallocatedModal.type === 'customer' ? 'bg-indigo-50 border-indigo-200 text-indigo-700' : 'bg-white border-gray-200 text-gray-500'" class="flex-1 border py-2 px-3 rounded-md text-sm font-medium transition-all">Customer</button>
                    <button type="button" @click="unallocatedModal.type = 'supplier'" :class="unallocatedModal.type === 'supplier' ? 'bg-indigo-50 border-indigo-200 text-indigo-700' : 'bg-white border-gray-200 text-gray-500'" class="flex-1 border py-2 px-3 rounded-md text-sm font-medium transition-all">Supplier</button>
                    <input type="hidden" name="type" :value="unallocatedModal.type">
                  </div>
                </div>
                
                <div v-if="unallocatedModal.type">
                  <label class="block text-sm font-medium text-gray-700">{{ unallocatedModal.type === 'customer' ? 'Customer' : 'Supplier' }}</label>
                  <select name="entity_id" required class="mt-1 block w-full py-2 px-3 border-2 border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <option value="">Select {{ unallocatedModal.type === 'customer' ? 'Customer' : 'Supplier' }}</option>
                    <option v-for="entity in (unallocatedModal.type === 'customer' ? customers : suppliers)" :key="entity.id" :value="entity.id">
                      {{ entity.name }}
                    </option>
                  </select>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700">Amount</label>
                  <div class="mt-1 relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <span class="text-gray-500 sm:text-sm">{{ currencySymbol }}</span>
                    </div>
                    <input type="number" name="amount" required step="0.01" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-7 pr-12 sm:text-sm border-gray-300 rounded-md border-2">
                  </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Date</label>
                        <input type="date" name="payment_date" :value="today" required class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md border-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Method</label>
                        <select name="payment_method" class="mt-1 block w-full py-2 px-3 border-2 border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="cash">Cash</option>
                            <option value="check">Check</option>
                        </select>
                    </div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700">Reference / Notes</label>
                  <input type="text" name="notes" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md border-2">
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse rounded-b-lg">
          <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
            Save Advance Payment
          </button>
          <button @click="unallocatedModal.open = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border-2 border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
            Cancel
          </button>
        </div>
      </form>
    </CommonModal>

    <!-- Allocation Modal -->
    <CommonModal :is-open="allocationModal.open" max-width="lg" @close="allocationModal.open = false">
      <form v-if="allocationModal.payment" method="POST" :action="`/${tenantSlug}/admin/payments/${allocationModal.payment.id}/allocate`" class="w-full">
        <input type="hidden" name="_token" :value="csrfToken">
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
          <div class="sm:flex sm:items-start">
            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10 text-green-600">
              <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
              <h3 class="text-lg leading-6 font-medium text-gray-900">Allocate Payment</h3>
              <div class="mt-2 text-sm text-gray-600">
                Allocating credit from <span class="font-bold">{{ allocationModal.payment.entity?.name }}</span>
                <br>
                Available: <span class="font-bold text-green-600">{{ currencySymbol }}{{ formatNumber(allocationModal.payment.unallocated_amount) }}</span>
              </div>
              
              <div class="mt-4 space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 text-left">Match to Outstanding {{ isCustomerPayment(allocationModal.payment) ? 'Order' : 'Invoice' }}</label>
                  <select name="allocatable_id" required class="mt-1 block w-full py-2 px-3 border-2 border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <option value="">Select {{ isCustomerPayment(allocationModal.payment) ? 'Order' : 'Invoice' }}</option>
                    <option v-for="item in getMatchingItems(allocationModal.payment)" :key="item.id" :value="item.id">
                      {{ isCustomerPayment(allocationModal.payment) ? item.order_number : item.invoice_number }} 
                      ({{ currencySymbol }}{{ formatNumber(item.total - item.amount_paid) }} due)
                    </option>
                  </select>
                  <input type="hidden" name="allocatable_type" :value="isCustomerPayment(allocationModal.payment) ? 'Order' : 'SupplierInvoice'">
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 text-left">Amount to Allocate</label>
                  <div class="mt-1 relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <span class="text-gray-500 sm:text-sm">{{ currencySymbol }}</span>
                    </div>
                    <input type="number" name="amount" required step="0.01" :max="allocationModal.payment.unallocated_amount" :value="allocationModal.payment.unallocated_amount" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-7 pr-12 sm:text-sm border-2 border-gray-300 rounded-md">
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse rounded-b-lg">
          <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
            Allocate Credit
          </button>
          <button @click="allocationModal.open = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border-2 border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
            Cancel
          </button>
        </div>
      </form>
    </CommonModal>
  </div>
</template>

<script setup>
import { ref, reactive, computed } from 'vue';
import CommonModal from '../common/CommonModal.vue';

const props = defineProps({
  receivables: {
    type: Array,
    default: () => []
  },
  payables: {
    type: Array,
    default: () => []
  },
  unallocatedPayments: {
    type: Array,
    default: () => []
  },
  customers: {
    type: Array,
    default: () => []
  },
  suppliers: {
    type: Array,
    default: () => []
  },
  receivablesTotal: Number,
  payablesTotal: Number,
  unallocatedTotal: Number,
  receivablesHasPages: Boolean,
  payablesHasPages: Boolean,
  unallocatedHasPages: Boolean,
  currencySymbol: String,
  tenantSlug: String,
  csrfToken: String,
  initialTab: {
    type: String,
    default: 'receivables'
  }
});

const activeTab = ref(props.initialTab);

const modal = reactive({
  open: false,
  type: '',
  id: null,
  amount: 0,
  ref: ''
});

const unallocatedModal = reactive({
  open: false,
  type: 'customer'
});

const allocationModal = reactive({
  open: false,
  payment: null
});

const today = new Date().toISOString().split('T')[0];

const openPaymentModal = (type, id, balance, refVal) => {
  modal.type = type;
  modal.id = id;
  modal.amount = balance;
  modal.ref = refVal;
  modal.open = true;
};

const openAllocationModal = (payment) => {
  allocationModal.payment = payment;
  allocationModal.open = true;
};

const isCustomerPayment = (payment) => {
  if (!payment) return false;
  return payment.entity_type.includes('Customer');
};

const getMatchingItems = (payment) => {
  if (!payment) return [];
  if (isCustomerPayment(payment)) {
      return props.receivables.filter(r => r.customer_id == payment.entity_id);
  } else {
      return props.payables.filter(p => p.supplier_id == payment.entity_id);
  }
};

const getOrderUrl = (id) => {
  return `/${props.tenantSlug}/admin/orders/${id}`;
};

const getPaymentUrl = () => {
  if (modal.type === 'customer') {
    return `/${props.tenantSlug}/admin/orders/${modal.id}/payment`;
  } else {
    return `/${props.tenantSlug}/admin/supplier-invoices/${modal.id}/payment`;
  }
};

const formatDate = (dateString) => {
  if (!dateString) return '';
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
};

const formatNumber = (num) => {
  if (num === null || num === undefined) return '0.00';
  return parseFloat(num).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
};
</script>
