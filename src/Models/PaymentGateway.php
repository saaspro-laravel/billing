<?php

namespace SaasPro\Billing\Models;

use Illuminate\Database\Eloquent\Model;
use SaasPro\Billing\Enums\PaymentGateways;
use SaasPro\Concerns\Models\HasStatus;

class PaymentGateway extends Model {
    use HasStatus;

    protected $fillable = ['name', 'shortcode', 'config', 'status'];

    function casts(){
        return [
            'shortcode' => PaymentGateways::class,
            'config' => 'encrypted:array'  
        ];
    }

}
