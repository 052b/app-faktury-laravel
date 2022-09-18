<?php

namespace App\Actions;

use App\Http\Requests\InvoiceSetPaidRequest;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Http\Request;

class InvoiceSetPaid
{
    public function __invoke(InvoiceSetPaidRequest $invoiceSetPaidRequest): void
    {
        $validated = $invoiceSetPaidRequest->validated();

        $invoice               = Invoice::findOrFail($validated['id']);
        $invoice->status       = true;
        $invoice->payment_date = $validated['datePaid'];
        $invoice->save();
    }
}
