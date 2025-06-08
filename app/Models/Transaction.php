<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'user_id',
        'customer_id', // Tambahkan 'customer_id' ke fillable
        'total_amount',
        'payment_amount',
        'change_amount',
    ];

    /**
     * Get the user (cashier) that owns the transaction.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function transactionItems()
{
    return $this->hasMany(TransactionItem::class);
}

    /**
     * Get the customer that owns the transaction.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the transaction items for the transaction.
     */
    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }
}
