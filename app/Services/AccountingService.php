<?php

namespace App\Services;

use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class AccountingService
{
    /**
     * Record a sale (Revenue Recognition)
     * Triggered when Order is 'completed' or 'delivered' depending on policy.
     * 
     * Dr. Accounts Receivable (or Cash if paid immediately, but let's stick to AR for invoice logic)
     * Cr. Sales Revenue
     * 
     * Dr. Cost of Goods Sold
     * Cr. Inventory Asset
     */
    public function recordSale(Order $order)
    {
        \Illuminate\Support\Facades\Log::info('AccountingService: recordSale called', ['order_id' => $order->id, 'status' => $order->status]);

        // Avoid duplicate entries
        if (JournalEntry::where('entity_type', Order::class)->where('entity_id', $order->id)->where('description', 'like', 'Sale Invoice%')->exists()) {
             \Illuminate\Support\Facades\Log::info('AccountingService: Duplicate entry found, skipping.');
            return;
        }

        $tenant = app('tenant');
        
        // Ensure Accounts Exist
        $arAccount = $this->getOrCreateAccount('Accounts Receivable', 'asset', 'current_asset');
        $revenueAccount = $this->getOrCreateAccount('Sales Revenue', 'revenue', 'operating_revenue');
        $cogsAccount = $this->getOrCreateAccount('Cost of Goods Sold', 'expense', 'cost_of_goods_sold');
        $inventoryAccount = $this->getOrCreateAccount('Inventory Asset', 'asset', 'current_asset');

        DB::transaction(function() use ($order, $arAccount, $revenueAccount, $cogsAccount, $inventoryAccount) {
            
            \Illuminate\Support\Facades\Log::info('AccountingService: Inside Transaction');

            // 1. Revenue Entry
            $entry = JournalEntry::create([
                'entry_date' => now(),
                'description' => "Sale Invoice #{$order->order_number}",
                'entity_type' => Order::class,
                'entity_id' => $order->id,
            ]);
            
            \Illuminate\Support\Facades\Log::info('AccountingService: Entry Created', ['entry_id' => $entry->id]);

            // Dr. AR
            $entry->lines()->create([
                'account_id' => $arAccount->id,
                'debit' => $order->total,
                'credit' => 0,
            ]);

            // Cr. Revenue
            $entry->lines()->create([
                'account_id' => $revenueAccount->id,
                'debit' => 0,
                'credit' => $order->total,
            ]);

            // 2. COGS / Inventory Entry (only for tracked items)
            // We need to calculate total cost. Assuming product->cost_price exists. 
            // If not, we skip COGS for now or use 0.
            
            $totalCost = 0;
            foreach ($order->items as $item) {
                if ($item->product && $item->product->track_inventory) {
                    $cost = $item->product->cost_price ?? 0;
                    $totalCost += ($cost * $item->quantity);
                }
            }

            if ($totalCost > 0) {
                $cogsEntry = JournalEntry::create([
                    'entry_date' => now(),
                    'description' => "COGS for Order #{$order->order_number}",
                    'entity_type' => Order::class,
                    'entity_id' => $order->id,
                ]);

                // Dr. COGS
                $cogsEntry->lines()->create([
                    'account_id' => $cogsAccount->id,
                    'debit' => $totalCost,
                    'credit' => 0,
                ]);

                // Cr. Inventory
                $cogsEntry->lines()->create([
                    'account_id' => $inventoryAccount->id,
                    'debit' => 0,
                    'credit' => $totalCost,
                ]);
            }
        });
    }

    /**
     * Record a Payment against an Order.
     * 
     * Dr. Cash/Bank
     * Cr. Accounts Receivable
     */
    public function recordPayment(Order $order, $amount, $method = 'cash')
    {
        $cashAccount = $this->getOrCreateAccount('Cash on Hand', 'asset', 'cash');
        $arAccount = $this->getOrCreateAccount('Accounts Receivable', 'asset', 'current_asset');

         DB::transaction(function() use ($order, $amount, $method, $cashAccount, $arAccount) {
            $entry = JournalEntry::create([
                'entry_date' => now(),
                'description' => "Payment for Order #{$order->order_number} ({$method})",
                'entity_type' => Order::class,
                'entity_id' => $order->id,
            ]);

            // Dr. Cash
            $entry->lines()->create([
                'account_id' => $cashAccount->id,
                'debit' => $amount,
                'credit' => 0,
            ]);

            // Cr. AR
            $entry->lines()->create([
                'account_id' => $arAccount->id,
                'debit' => 0,
                'credit' => $amount,
            ]);
         });
    }

    private function getOrCreateAccount($name, $type, $subType)
    {
        return Account::firstOrCreate(
            ['name' => $name],
            [
                'account_type' => $type,
                'sub_ledger_type' => $subType,
                'code' => $this->generateCode($type),
                'is_active' => true,
                'description' => "System generated account for {$name}",
            ]
        );
    }
    
    private function generateCode($type)
    {
        // Simple code generation strategy
        $prefix = match($type) {
            'asset' => '1',
            'liability' => '2',
            'equity' => '3',
            'revenue' => '4',
            'expense' => '5',
            default => '9'
        };
        
        return $prefix . rand(1000, 9999);
    }
}
