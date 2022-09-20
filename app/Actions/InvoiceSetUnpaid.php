<?php

namespace App\Actions;

use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceSetUnpaid
{
    public function __invoke(Request $request): void
    {
        $validated = $request->validate([
            'id' => 'required|exists:site_invoices'
        ]);

        $invoice               = Invoice::findOrFail($validated['id']);
        $invoice->status       = false;
        $invoice->payment_date = null;
        $invoice->save();
    }
}
