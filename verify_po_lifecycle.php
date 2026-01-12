<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$tenant = \App\Models\Tenant::where('slug', 'dplux')->first();
if (!$tenant) {
    // Try to find any tenant if 'dplux' doesn't exist
    $tenant = \App\Models\Tenant::first();
}
if (!$tenant) {
    die("❌ No tenants found in the database. Run setup first.\n");
}
echo "Using Tenant: {$tenant->slug} (ID: {$tenant->id})\n";
app()->instance('tenant', $tenant);
config(['app.tenant_id' => $tenant->id]);
\Illuminate\Support\Facades\URL::defaults(['tenant' => $tenant->slug]);

// Mock Auth
$admin = \App\Models\User::where('role', 'admin')->first();
if ($admin) {
    auth()->login($admin);
}

use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

echo "--- PO Lifecycle Verification ---\n";

$supplier = Supplier::first();
$warehouse = Warehouse::first();
$product = Product::first();

if (!$supplier || !$warehouse || !$product) {
    die("❌ Missing seed data. Run fix_tenant_db.php first.\n");
}

// 1. Create PO
echo "1. Creating PO...\n";
$po = PurchaseOrder::create([
    'po_number' => 'TEST-PO-' . time(),
    'tenant_id' => 'dplux',
    'supplier_id' => $supplier->id,
    'warehouse_id' => $warehouse->id,
    'order_date' => now(),
    'status' => 'draft',
    'subtotal' => 0,
    'tax' => 0,
    'total' => 0,
    'created_by' => $admin->id,
]);

$po->items()->create([
    'product_id' => $product->id,
    'quantity_ordered' => 10,
    'unit_cost' => 50,
    'total' => 500,
]);

$po->update(['subtotal' => 500, 'total' => 500]);
echo "✅ PO created: {$po->po_number}\n";

// 2. Place Order
echo "2. Placing PO...\n";
$controller = app(\App\Http\Controllers\Admin\PurchaseOrderController::class);
$controller->placeOrder($po);
$po->refresh();
echo "✅ PO status: {$po->status}\n";

// 3. Receive Stock
echo "3. Receiving Stock...\n";
$initialStock = $product->stock_quantity;
echo "Initial Stock: $initialStock\n";
$controller->receive($po);
$po->refresh();
$product->refresh();
echo "✅ PO status: {$po->status}\n";
echo "New Stock: {$product->stock_quantity} (Change: " . ($product->stock_quantity - $initialStock) . ")\n";

// 4. Verify Accounting (Goods Receipt)
echo "4. Verifying Goods Receipt JE...\n";
$latestJE = \App\Models\JournalEntry::orderBy('id', 'desc')->first();
echo "Latest JE: {$latestJE->description}\n";
foreach ($latestJE->lines as $line) {
    echo "- {$line->account->account_code}: Dr {$line->debit}, Cr {$line->credit}\n";
}

// 5. Convert to Bill
echo "5. Converting to Bill...\n";
$controller->convertToBill(new \Illuminate\Http\Request(['invoice_number' => 'INV-TEST-001']), $po);
$po->refresh();
echo "✅ PO billed_status: {$po->billed_status}\n";

// 6. Verify Accounting (Bill)
echo "6. Verifying Bill JE...\n";
$latestJE = \App\Models\JournalEntry::orderBy('id', 'desc')->first();
echo "Latest JE: {$latestJE->description}\n";
foreach ($latestJE->lines as $line) {
    echo "- {$line->account->account_code}: Dr {$line->debit}, Cr {$line->credit} (Entity: {$line->entity_id})\n";
}

// 7. Return items
echo "7. Returning items...\n";
$request = new \Illuminate\Http\Request();
$request->merge([
    'items' => [
        $po->items->first()->id => ['quantity' => 2]
    ]
]);
$controller->returnsStore($request, $po);
$product->refresh();
echo "✅ New Stock after return: {$product->stock_quantity}\n";

// 8. Verify Accounting (Return)
echo "8. Verifying Return JE...\n";
$jes = \App\Models\JournalEntry::orderBy('id', 'desc')->take(3)->get();
foreach ($jes as $idx => $latestJE) {
    echo "JE #" . ($idx + 1) . ": {$latestJE->description} (ID: {$latestJE->id})\n";
    foreach ($latestJE->lines as $line) {
        echo "- {$line->account->account_code}: Dr {$line->debit}, Cr {$line->credit} (Entity: {$line->entity_id})\n";
    }
    if (str_contains($latestJE->description, 'Purchase Return')) break;
}

echo "\n🎉 Lifecycle Verification Completed!\n";
