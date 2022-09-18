<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoicePosition extends Model
{
    use HasFactory;

    protected $table      = 'site_invoices_positions';
    public    $timestamps = false;

    protected $fillable = [
        'id_invoice',
        'name',
        'quantity',
        'price_single_netto',
        'price_netto',
        'vat_type',
        'price_single_brutto',
        'price_brutto'
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'id', 'id_invoice');
    }
}
