<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = ['date', 'total', 'payment_type_id', 'user_id'];

    protected $casts = [
        'date' => 'date',
        'total' => 'integer',
    ];

    public function details()
    {
        return $this->hasMany(TransactionDetail::class);
    }

    public function paymentType()
    {
        return $this->belongsTo(PaymentType::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
