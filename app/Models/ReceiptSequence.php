<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReceiptSequence extends Model
{
    protected $fillable = [
        'financial_year',
        'last_sequence',
    ];

    protected $casts = [
        'last_sequence' => 'integer',
    ];

    public $timestamps = true;
}
