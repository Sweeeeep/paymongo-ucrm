<?php

declare(strict_types=1);

namespace Sweeeeep\PaymongoUcrm\Services;

use Sweeeeep\PaymongoUcrm\Data\PaymentTokenData;

class SmsNumberProvider {
    public function getUcrmClientNumber(PaymentTokenData $clientData){
        $contacts = $clientData->contacts ?? [];
        foreach($contacts as $contact){
            return $this->isClientNumberValid($contact['phone']) ? $contact['phone'] : false;
        }
    }

    private function isClientNumberValid($phone){
        if(!is_null($phone)) return preg_match('/^(09|\+?639)\d{9}$/', $phone);
        return false;
    }
}