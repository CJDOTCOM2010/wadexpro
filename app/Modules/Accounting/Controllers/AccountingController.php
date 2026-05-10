<?php

namespace App\Modules\Accounting\Controllers;

use App\Core\Traits\ApiResponse;
use App\Modules\Accounting\Models\Invoice;
use App\Modules\Accounting\Models\JournalEntry;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AccountingController extends Controller
{
    use ApiResponse;

    /**
     * List all journal entries for the ledger.
     */
    public function journalIndex()
    {
        $entries = JournalEntry::with('lines.account')->latest('entry_date')->paginate(20);
        return $this->paginated($entries, 'Ledger retrieved.');
    }

    /**
     * List all invoices for the treasury UI.
     */
    public function invoiceIndex()
    {
        $invoices = Invoice::with('customer')->latest('issue_date')->paginate(15);
        return $this->paginated($invoices, 'Invoices retrieved.');
    }

    /**
     * Retrieve a single invoice with line items.
     */
    public function invoiceShow(string $id)
    {
        $invoice = Invoice::with(['customer', 'lineItems', 'order'])->findOrFail($id);
        return $this->success($invoice, 'Invoice details.');
    }
}
