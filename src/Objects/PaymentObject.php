<?php

namespace SaasPro\Billing\Objects;

use Illuminate\Database\Eloquent\Model;
use SaasPro\Abstracts\DataObject;
use SaasPro\Billing\Contracts\Billable;
use SaasPro\Billing\Enums\PaymentStatus;
use SaasPro\Billing\Models\BillingAddress;
use SaasPro\Billing\Models\Customer;
use SaasPro\Billing\Models\Invoice;
use SaasPro\Billing\Models\Transaction;
use SaasPro\Enums\Status;
use SaasPro\Locale\Models\Country;
use SaasPro\Locale\Models\Currency;

class PaymentObject extends DataObject {

    function __construct(
        public ?int $amount = null,
        public ?Customer $customer = null,
        public ?Invoice $invoice = null,
        public ?Model $billable = null,
        public ?Transaction $transaction = null,
        public ?BillingAddress $billingAddress = null,
        public ?Country $country = null,
        public ?Currency $currency = null,
        public int $quantity = 1,
    ) { }

    function billable(?Model $billable = null) {
        $this->billable = $billable;
        
        if($column = $this->billable->getBillableAmount()) {
            $this->amount = $this->billable->{$column};
        }

        return $this;
    }

    static function fromInvoice(Invoice $invoice) {
        $invoice->load(['billable', 'customer']);

        return new self(
            amount: $invoice->amount,
            billable: $invoice->billable,
            invoice: $invoice,
            customer: $invoice->customer
        );
    }

    function invoice(?Invoice $invoice = null){
        if($invoice) $this->invoice = $invoice;

        if(!$this->invoice) {
            $this->invoice = new Invoice();
            $this->invoice->amount = $this->amount;
            $this->invoice->attach($this->customer);
            $this->invoice->save();
        }

        $this->invoice->load(['billable', 'customer']);
        return $this;
    }

    function initiate(?PaymentStatus $paymentStatus = PaymentStatus::PENDING){
        $this->transaction = $this->invoice->transactions()->create([
            'amount' => $this->amount,
            'status' => $paymentStatus
        ]);

        return $this;
    }

    function callback(){
        // return
    }

}