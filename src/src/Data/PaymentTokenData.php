<?php

declare(strict_types=1);

namespace Sweeeeep\PaymongoUcrm\Data;

class PaymentTokenData extends ClientData{

    /** @var string */
    public $token;

    /** @var int|null */
    public $clientId;

    /** @var int|null */
    public $invoiceId;

    /** @var float */
    public $amount;

}