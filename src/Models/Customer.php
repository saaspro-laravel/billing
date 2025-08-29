<?php

namespace SaasPro\Billing\Models;

use Illuminate\Database\Eloquent\Model;
use SaasPro\Billing\Models\Transaction;

class Customer extends Model {

    protected $fillable = ['name', 'email', 'phone'];

    function profile(){
        return $this->morphTo();
    }

    function transactions(){
        return $this->hasMany(Transaction::class);
    }

    function invoices(){
        return $this->hasMany(Invoice::class);
    }

    

}