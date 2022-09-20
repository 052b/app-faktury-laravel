<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $table      = 'site_contractor';
    public    $timestamps = false;

    protected $fillable = [
        'odoo_id',
        'id_company',
        'name',
        'address',
        'address2',
        'zip',
        'city',
        'nip',
        'phone',
        'mail',
        'type',
        'deleted',
        'key_client'
    ];

    protected $casts = [
        'deleted' => 'boolean'
    ];

    public function invoices(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Invoice::class,  'id_contractor','id');
    }
}
