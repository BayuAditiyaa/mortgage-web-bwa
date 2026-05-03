<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentTransaction extends Model
{
    protected $fillable = [
        'mortgage_request_id',
        'installment_id',
        'order_id',
        'snap_token',
        'gross_amount',
        'transaction_status',
        'payload',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'paid_at' => 'datetime',
        ];
    }

    public function mortgageRequest()
    {
        return $this->belongsTo(MortgageRequest::class);
    }

    public function installment()
    {
        return $this->belongsTo(Installment::class);
    }
}
