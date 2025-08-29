<?php

namespace SaasPro\Billing\Models;

use Illuminate\Database\Eloquent\Model;
use SaasPro\Billing\Enums\InvoiceStatus;
use SaasPro\Billing\Models\Transaction;
use SaasPro\Concerns\Models\HasReference;
use SaasPro\Concerns\Models\HasStatus;
use SaasPro\Locale\Models\Country;
use SaasPro\Locale\Models\Currency;

class Invoice extends Model {
    use HasStatus, HasReference;

    protected $fillable = ['customer_id', 'amount', 'currency_code', 'due_date', 'balance', 'billing_address_id'];

    static function booted(){
        self::creating(function(Invoice $invoice){
            $country = Country::current();
            $invoice->currency()->associate($country->currency);

            if(!$invoice->reference) {
                $invoice->reference = $invoice->generateReference('INV-');
            }
        });
    }

    function setStatusEnum(){
        return InvoiceStatus::class;
    }

    function billable(){
        return $this->morphTo();
    }

    function currency(){
        return $this->belongTo(Currency::class, 'currency_code');
    }

    function customer(){
        return $this->belongsTo(Customer::class);
    }

    function transactions(){
        return $this->morphMany(Transaction::class, 'transactable');
    }

    function billingAddress(){
        return $this->hasOne(BillingAddress::class);
    }

    function payment(){
        
    }

    

}