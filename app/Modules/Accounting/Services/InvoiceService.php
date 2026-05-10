<?php

namespace App\Modules\Accounting\Services;

use App\Modules\Accounting\Models\Invoice;
use App\Modules\Accounting\Models\InvoiceLineItem;
use App\Modules\Logistics\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InvoiceService
{
    /**
     * Generate an invoice from a completed Order.
     */
    public function generateFromOrder(Order $order): Invoice
    {
        return DB::transaction(function () use ($order) {
            $invoice = Invoice::create([
                'invoice_number' => 'INV-' . strtoupper(Str::random(8)),
                'customer_id' => $order->customer_id,
                'order_id' => $order->id,
                'issue_date' => now(),
                'due_date' => now()->addDays(7),
                'subtotal' => $order->subtotal,
                'tax_amount' => $order->tax_amount,
                'total' => $order->total_amount,
                'currency' => $order->currency,
                'status' => $order->payment_status === 'paid' ? 'paid' : 'sent',
                'paid_at' => $order->payment_status === 'paid' ? now() : null,
            ]);

            // Create Delivery Fee line item
            InvoiceLineItem::create([
                'invoice_id' => $invoice->id,
                'description' => "Logistics Delivery Fee",
                'quantity' => 1,
                'unit_price' => $order->delivery_fee,
                'line_total' => $order->delivery_fee,
                'sort_order' => 1,
            ]);

            return $invoice;
        });
    }
}
