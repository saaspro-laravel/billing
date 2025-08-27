<?php

namespace SaasPro\Billing\Models;

use Illuminate\Database\Eloquent\Model;
use SaasPro\Billing\Models\Transaction;

class Invoice extends Model {

    protected $fillable = ['customer_id', 'amount', 'status', 'due_date', 'billing_address_id'];

    function customer(){
        return $this->belongsTo(Customer::class);
    }

    function transactions(){
        return $this->hasMany(Transaction::class);
    }

    

}