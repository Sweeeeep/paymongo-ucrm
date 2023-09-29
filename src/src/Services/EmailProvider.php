<?php

declare(strict_types=1);

namespace Sweeeeep\PaymongoUcrm\Service;

use Sweeeeep\PaymongoUcrm\Data\PaymentTokenData;

class EmailProvider {
    public function getUcrmClientEmail(PaymentTokenData $clientData){
        $contacts = $clientData->contacts ?? [];
        foreach($contacts as $contact){
            return $this->isEmailAddressValid($contact['email']) ? $contact['email'] : false;
        }
    }

    private function isEmailAddressValid($email){
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }
}