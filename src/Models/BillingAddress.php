<?php

namespace SaasPro\Billing\Models;

use Illuminate\Database\Eloquent\Model;

class BillingAddress extends Model {

    protected $fillable = ['customer_id', 'line1', 'line2', 'city', 'state', 'postal_code', 'country', 'is_default'];

    public function customer(){
        return $this->belongsTo(Customer::class);
    }
    

}