<?php

namespace Utyemma\SaasPro\Models;

use Illuminate\Database\Eloquent\Model;
use Utyemma\SaasPro\Concerns\Models\HasStatus;
use Utyemma\SaasPro\Enums\PaymentGateways;

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
