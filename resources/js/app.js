import { OfflineManager } from './offline-manager';

window.OfflineManager = OfflineManager;

import Alpine from 'alpinejs';
import { createApp, reactive } from 'vue';
import StorefrontSettings from './components/storefront/StorefrontSettings.vue';
import PurchaseOrderForm from './components/PurchaseOrderForm.vue';
import NotificationDropdown from './components/layout/NotificationDropdown.vue';
import UserDropdown from './components/layout/UserDropdown.vue';
import SidebarToggle from './components/layout/SidebarToggle.vue';
import MobileSidebar from './components/layout/MobileSidebar.vue';
import MobileBottomNav from './components/layout/MobileBottomNav.vue';
import OfflineIndicator from './components/utils/OfflineIndicator.vue';
import OutstandingPayments from './components/payments/OutstandingPayments.vue';
import ProductList from './components/products/ProductList.vue';
import ProductForm from './components/products/ProductForm.vue';
import BulkUploadManager from './components/products/BulkUploadManager.vue';
import PosSystem from './components/pos/PosSystem.vue';
import CustomerDisplay from './components/pos/CustomerDisplay.vue';
import GeneralSettings from './components/settings/GeneralSettings.vue';
import StoreWizard from './components/wizard/StoreWizard.vue';
import OrderCreate from './components/orders/OrderCreate.vue';
import OmniChannelOrders from './components/orders/OmniChannelOrders.vue';
import MessageInbox from './components/messages/MessageInbox.vue';
import StoreContent from './components/storefront/StoreContent.vue';
import TenantLogin from './components/auth/TenantLogin.vue';
import DomainSettings from './components/settings/DomainSettings.vue';
import InventoryReport from './components/admin/reports/InventoryReport.vue';

import { EditorStore } from './cms/store';
import editable from './cms/editable';

window.Alpine = Alpine;

// Global UI State
import StockTransferForm from './components/admin/stock-transfers/StockTransferForm.vue';

const uiState = reactive({
    sidebarOpen: false
});
// ... 

window.uiState = uiState;

// Initialize Vue for specific elements
const app = createApp({
    setup() {
        return { uiState };
    }
});

app.component('stock-transfer-form', StockTransferForm);
app.component('storefront-settings', StorefrontSettings);
app.component('purchase-order-form', PurchaseOrderForm);
app.component('notification-dropdown', NotificationDropdown);
app.component('user-dropdown', UserDropdown);
app.component('sidebar-toggle', SidebarToggle);
app.component('mobile-sidebar', MobileSidebar);
app.component('mobile-bottom-nav', MobileBottomNav);
app.component('offline-indicator', OfflineIndicator);
app.component('outstanding-payments', OutstandingPayments);
app.component('product-list', ProductList);
app.component('product-form', ProductForm);
app.component('bulk-upload-manager', BulkUploadManager);
app.component('pos-system', PosSystem);
app.component('customer-display', CustomerDisplay);
app.component('general-settings', GeneralSettings);
app.component('store-wizard', StoreWizard);
app.component('order-create', OrderCreate);
app.component('omni-channel-orders', OmniChannelOrders);
app.component('message-inbox', MessageInbox);
app.component('store-content', StoreContent);
app.component('tenant-login', TenantLogin);
app.component('domain-settings', DomainSettings);
app.component('inventory-report', InventoryReport);


app.mount('#app');

Alpine.store('editor', EditorStore);
Alpine.data('editable', editable);

Alpine.start();
