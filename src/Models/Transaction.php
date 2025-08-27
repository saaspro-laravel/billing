<?php

namespace SaasPro\Billing\Models;

use SaasPro\Billing\Enums\PaymentGateways;
use SaasPro\Billing\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use SaasPro\Concerns\Models\HasHistory;
use SaasPro\Contracts\SavesToHistory;
use SaasPro\Locale\Models\Country;
use SaasPro\Locale\Models\Currency;
use SaasPro\Support\Token;

class Transaction extends Model implements SavesToHistory {
    use SoftDeletes, HasHistory;
    
    protected $fillable = ['reference', 'currency_code', 'country_code', 'gateway', 'provider_id', 'payload','response', 'amount', 'status'];

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

            if(!$transaction->reference) {
                $transaction->reference = Token::random(8)->prepend('TXN-')->upper()->unique(self::class, 'reference');
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
