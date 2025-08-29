<?php

namespace SaasPro\Billing;

class Billing {

    protected $billable;

    function make($billable){
        $this->billable = $billable->customerProfile ??= $billable->create();
        return $this;
    }


}