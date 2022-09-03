<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $table = 'site_contractor';

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
}
