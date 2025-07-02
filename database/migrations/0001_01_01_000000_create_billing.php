<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void {
        
        Schema::create('payment_gateways', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('shortcode')->unique();
            $table->longText('config');
            $table->string('status');
            $table->timestamps();
        });

        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->json('payload');
            $table->double('amount');
            $table->string('status');
            $table->string('gateway');
            $table->string('reference');
            $table->morphs('transactable');
            $table->string('currency_code');
            $table->string('country_code');
            $table->string('provider_id')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('transaction_histories', function (Blueprint $table) {
            $table->id();
            $table->string('status');
            $table->string('transaction_id');
            $table->json('meta');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_gateways');
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('transaction_histories');
    }
};
