<?php

namespace Utyemma\SaasPro\Models\Transactions;

use Utyemma\SaasPro\Enums\PaymentGateways;
use Utyemma\SaasPro\Enums\PaymentStatus;
use Utyemma\SaasPro\Enums\Transactions;
use Utyemma\SaasPro\Models\Country;
use Utyemma\SaasPro\Models\Currency;
use Utyemma\SaasPro\Models\Subscription;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model {
    use SoftDeletes;
    
    protected $fillable = ['reference', 'currency_code', 'country_code', 'gateway', 'provider_id', 'payload', 'transactable_id', 'transactable_type', 'response', 'amount', 'status'];

    protected $casts = [
        'gateway' => PaymentGateways::class,
        'status' => PaymentStatus::class,
        'payload' => 'array'
    ];

    protected $attributes = [
        'status' => PaymentStatus::PENDING
    ];

    static function booted(){
        self::creating(function(Transaction $transaction){
            $country = Country::current();
            $transaction->country()->associate($country);
            $transaction->currency()->associate($country->currency);
        });
    }

    function transactable(){
        return $this->morphTo();
    }

    function country(){
        return $this->belongsTo(Country::class, 'country_code', 'iso_code');
    }

    function currency(){
        return $this->belongsTo(Currency::class, 'currency_code', 'code');
    }

    function provider() {
        return $this->gateway->provider();
    } 

    function history(){
        return $this->hasMany(TransactionHistory::class, 'transaction_id');
    }

    function getIsSubcriptionAttribute(){
        return $this->transactable_type == Subscription::class;
    }

    function getTypeAttribute(){
        return match($this->transactable_type) {
            Subscription::class => Transactions::SUBSCRIPTION,
            default => Transactions::PAYMENT
        };
    }
    

    function saveHistory($data){
        $this->history()->create([
            'meta' => $data,
            'status' => $this->status
        ]);
    }

}
