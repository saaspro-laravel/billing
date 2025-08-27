<?php

use SaasPro\Billing\Models\Customer;

trait IsBillable {

    function customerProfile(){
        return $this->hasOne(Customer::class);
    }

    

}