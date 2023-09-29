<?php

namespace Sweeeeep\PaymongoUcrm\Data;

class PaymongoCheckoutData {
    
    public $payment_method_types;

    public $billing;

    public $description;

    public $line_items;

    public $reference_number;

    public $send_email_receipt = true;

    public $show_description = true;

    public $show_line_items = false;

    public $success_url;
    
    public $cancel_url;

    public $statement_descriptor;
    
}