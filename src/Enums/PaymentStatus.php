<?php

namespace SaasPro\Billing\Enums;

enum PaymentStatus:string {

    case PENDING = 'pending'; //The transaction has been created but no payment attempt has been made yet
    case SUCCESS = 'success'; //A payment attempt was initiated and was successful
    case FAILED = 'failed'; // A payment attempt was initiated but failed
    case CANCELLED = 'cancelled'; //A payment attempt was initiated and cancelled by the user //For Admin only
    case ERROR = 'error'; // There was an error while initiating the payment or verifying the transaction //For Admin only
    case REVERSED = 'reversed'; // The payment was reversed

    function label(){
        return match($this){
            self::PENDING => 'Pending',
            self::SUCCESS => 'Success',
            self::FAILED => 'Failed',
            self::CANCELLED => 'Cancelled',
            self::ERROR => 'Error',
            self::REVERSED => 'Reversed',
        };
    }

    function message(){
        return match($this){
            self::PENDING => 'Transaction pending. Please check back later!',
            self::SUCCESS => 'Transaction completed successfully.',
            self::FAILED => 'Transaction failed. Please try again or contact our support center',
            self::CANCELLED => 'Transaction cancelled',
            self::ERROR => 'There was an error while preforming this transaction',
            self::REVERSED => 'The funds were resversed to the user'
        };
    }

}