<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
    ];

    /**
     * Get the transactions associated with the customer.
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
