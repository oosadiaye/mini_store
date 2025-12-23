<?php

namespace App\Services;

use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\Account;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class JournalEntryService
{
    /**
     * Record a balanced journal entry.
     *
     * @param string $description
     * @param array $lines [['account_code' => '1010', 'debit' => 100, 'credit' => 0]]
     * @param string|null $date
     * @return JournalEntry
     * @throws \Exception
     */
    public function recordTransaction(string $description, array $lines, $date = null)
    {
        return DB::transaction(function () use ($description, $lines, $date) {
            // Validate Balance
            $totalDebit = collect($lines)->sum('debit');
            $totalCredit = collect($lines)->sum('credit');

            if (abs($totalDebit - $totalCredit) > 0.01) {
                throw new \Exception("Journal Entry Not Balanced. Debit: $totalDebit, Credit: $totalCredit");
            }

            // Create Header
            $entry = JournalEntry::create([
                'entry_number' => 'TEMP-' . Str::uuid(),
                'entry_date' => $date ?? now(),
                'description' => $description,
                'created_by' => auth()->id() ?? 1, // Fallback for system automations
            ]);
            $entry->update(['entry_number' => str_pad($entry->id, 6, '0', STR_PAD_LEFT)]);

            // Create Lines
            foreach ($lines as $line) {
                // Find Account ID by Code
                $account = Account::where('account_code', $line['account_code'])->first();
                
                if (!$account) {
                    throw new \Exception("Account Code {$line['account_code']} not found in Chart of Accounts.");
                }

                if ($account->sub_ledger_type) {
                    if (empty($line['entity_type']) || empty($line['entity_id'])) {
                        throw new \Exception("Account '{$account->account_name}' is a control account for {$account->sub_ledger_type}s. A specific {$account->sub_ledger_type} must be selected.");
                    }
                }

                JournalEntryLine::create([
                    'journal_entry_id' => $entry->id,
                    'account_id' => $account->id,
                    'debit' => $line['debit'] ?? 0,
                    'credit' => $line['credit'] ?? 0,
                    'description' => $line['description'] ?? $description,
                    'entity_type' => $line['entity_type'] ?? null,
                    'entity_id' => $line['entity_id'] ?? null,
                ]);
            }

            return $entry;
        });
    }
}
