<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $table      = 'site_invoices';
    public    $timestamps = false;

    protected $fillable = [
        'type',
        'invoice_number',
        'id_contractor',
        'create_date',
        'sell_date',
        'invoice_term',
        'status',
        'deleted',
        'payment_date',
        'start_date',
        'end_date',
        'invoice_term_date',
    ];

    protected $casts = [
        'create_date'       => 'date:Y-m-d',
        'sell_date'         => 'date:Y-m-d',
        'payment_date'      => 'date:Y-m-d',
        'start_date'        => 'date',
        'end_date'          => 'date',
        'invoice_term_date' => 'date:Y-m-d',
        'deleted'           => 'boolean',
        'status'            => 'boolean'
    ];

    public function positions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(InvoicePosition::class, 'id_invoice', 'id');
    }

    public function client(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Client::class, 'id_contractor', 'id');
    }

//    public function scopeOverdue($query)
//    {
//        return $query->where('status', false)->where('invoice_term_date', '<', now());
//    }
//
//    public function scopeCostInvoice($query)
//    {
//        return $query->where('type', 10)->where('deleted', false);
//    }
//    public function scopeInvoice($query)
//    {
//        return $query->where('type', 0)->where('deleted', false);
//    }
}
