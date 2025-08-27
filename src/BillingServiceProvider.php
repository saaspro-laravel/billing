<?php

namespace SaasPro\Billing;

use Illuminate\Support\ServiceProvider;

class BillingServiceProvider extends ServiceProvider {

    function register() {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }

    function boot(){
        
    }
}