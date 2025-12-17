<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemsTransaction extends Model
{
    protected $fillable = [
    'item_code',
    'item_name',
    'quantity',
    'unit',
    'trx_date',
    'trx_time',
    'cost_center',
    'unit_cost',
    'total'
];
}
