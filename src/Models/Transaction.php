<?php

namespace SaasPro\Billing\Models;

use SaasPro\Billing\Enums\PaymentGateways;
use SaasPro\Billing\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use SaasPro\Concerns\Models\HasHistory;
use SaasPro\Concerns\Models\HasReference;
use SaasPro\Contracts\SavesToHistory;
use SaasPro\Locale\Models\Country;
use SaasPro\Locale\Models\Currency;
use SaasPro\Support\Token;

class Transaction extends Model implements SavesToHistory {
    use SoftDeletes, HasHistory, HasReference;
    
    protected $fillable = ['reference', 'currency_code', 'provider_id', 'payload', 'amount', 'status'];

    protected $casts = [
        'status' => PaymentStatus::class,
        'payload' => 'array'
    ];

    protected $attributes = [
        'status' => PaymentStatus::PENDING
    ];

    static function booted(){
        self::creating(function(Transaction $transaction){
            $country = Country::current();
            $transaction->currency()->associate($country->currency);

            if(!$transaction->reference) {
                $transaction->reference = $transaction->generateReference('TXN-');
            }
        });
    }

    function transactable(){
        return $this->morphTo();
    }

    function customer() {
        return $this->morphTo();
    }

    function country(){
        return $this->belongsTo(Country::class, 'country_code', 'iso_code');
    }

    function currency(){
        return $this->belongsTo(Currency::class, 'currency_code', 'code');
    }

    function getHistoryEvent($event){
        return $this->status;
    }

    function getHistoryEntityName(): string{
        return "Subscription";
    }

    function getHistoryEditorName(): string{
        return $this->subscriber_title;
    }


}
