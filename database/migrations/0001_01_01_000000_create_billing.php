<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->morphs('transactable');
            $table->double('amount');
            $table->string('reference');
            $table->string('currency_code')->nullable();
            $table->string('status');
            $table->json('payload');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('customers', function (Blueprint $table){
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->nullableMorphs('profile');
        });

        Schema::create('invoices', function (Blueprint $table){
            $table->id();
            $table->string('customer_id');
            $table->integer('amount');
            $table->integer('reference');
            $table->string('status');
            $table->string('due_date')->nullable();
            $table->string('currency_code')->nullable();
            $table->string('billing_address_id');
            $table->nullableMorphs('billable');
        });

        Schema::create('billing_addresses', function (Blueprint $table){
            $table->id();
            $table->string('customer_id');
            $table->integer('line1')->nullable();
            $table->string('line2')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->nullable();
            $table->boolean('is_default');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('billing_addresses');
    }
};
