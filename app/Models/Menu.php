<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    public const CATEGORIES = ['Makanan', 'Minuman', 'Snack'];

    protected $fillable = ['name', 'price', 'category', 'is_active'];

    protected $casts = [
        'price' => 'integer',
        'is_active' => 'boolean',
    ];

    public function details()
    {
        return $this->hasMany(TransactionDetail::class);
    }
}
