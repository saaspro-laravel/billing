<?php
namespace SaasPro\Billing\Concerns;

use SaasPro\Billing\Models\Customer;

trait HasCustomerProfile {

    public function customerProfile(){
        return $this->hasOne(Customer::class);
    }

    public function invoices(){
        return $this->through('customerProfile')->has('invoices');
    }

    public function transactions(){
        return $this->through('customerProfile')->has('transactions');
    }

    

}