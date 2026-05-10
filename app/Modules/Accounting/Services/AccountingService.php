<?php

namespace App\Modules\Accounting\Services;

use App\Modules\Accounting\Models\ChartOfAccount;
use App\Modules\Accounting\Models\JournalEntry;
use App\Modules\Accounting\Models\JournalLine;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AccountingService
{
    /**
     * Creates a balanced journal entry.
     */
    public function createEntry(array $data): JournalEntry
    {
        return DB::transaction(function () use ($data) {
            $totalDebit = collect($data['lines'])->sum('debit');
            $totalCredit = collect($data['lines'])->sum('credit');

            // Float precision guard: comparison with small epsilon
            if (abs($totalDebit - $totalCredit) > 0.0001) {
                throw new \Exception("Journal Entry must be balanced. Total Debit: {$totalDebit}, Total Credit: {$totalCredit}");
            }

            $entry = JournalEntry::create([
                'reference' => $data['reference'] ?? 'JV-' . strtoupper(Str::random(10)),
                'description' => $data['description'] ?? '',
                'entry_date' => $data['entry_date'] ?? now(),
                'source_type' => $data['source_type'] ?? 'manual',
                'source_id' => $data['source_id'] ?? null,
                'is_posted' => $data['is_posted'] ?? true,
                'created_by' => auth()->id(),
                'posted_at' => ($data['is_posted'] ?? true) ? now() : null,
            ]);

            foreach ($data['lines'] as $line) {
                JournalLine::create([
                    'journal_id' => $entry->id,
                    'account_id' => $line['account_id'],
                    'debit' => $line['debit'] ?? 0,
                    'credit' => $line['credit'] ?? 0,
                    'currency' => $line['currency'] ?? 'GHS',
                    'description' => $line['description'] ?? $entry->description,
                ]);
            }

            return $entry;
        });
    }

    /**
     * Helper to find system accounts by code.
     */
    public function getAccountByCode(string $code): ChartOfAccount
    {
        return ChartOfAccount::where('code', $code)->firstOrFail();
    }
}
