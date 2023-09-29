<?php

declare(strict_types=1);

namespace Sweeeeep\PaymongoUcrm\Factory;

use Sweeeeep\PaymongoUcrm\Data\PaymentTokenData;
use Sweeeeep\PaymongoUcrm\Data\PaymongoCheckoutData;
use Sweeeeep\PaymongoUcrm\Service\EmailProvider;
use Sweeeeep\PaymongoUcrm\Service\SmsNumberProvider;
use Sweeeeep\PaymongoUcrm\Services\OptionManager;

class PaymongoCheckoutFactory{
    
    const REDIRECT_SUCCESS = 'payment-success';
    const REDIRECT_FAILED = 'payment-failed';
    
    private $emailProvider;
    private $smsNumberProvider;
    private $pluginData;
    

    public function __construct(
        OptionManager $optionManager,
        EmailProvider $emailProvider,
        SmsNumberProvider $smsNumberProvider
    ) {
        $this->pluginData = $optionManager->load();
        $this->emailProvider = $emailProvider;
        $this->smsNumberProvider = $smsNumberProvider;
    }

    public function getParsedObject(PaymentTokenData $paymentTokenData) : PaymongoCheckoutData {
        $paymongoCheckoutData = new PaymongoCheckoutData();

        $paymongoCheckoutData->billing = [
            'name' => "{$paymentTokenData->firstName} {$paymentTokenData->lastName}",
            'email' => $this->emailProvider->getUcrmClientEmail($paymentTokenData),
            'phone' => $this->smsNumberProvider->getUcrmClientNumber($paymentTokenData)
        ];

        $paymongoCheckoutData->line_items[] = [
            'amount' => ($paymentTokenData->amount * 100),
            'currency' => 'PHP',
            'name' => 'Internet Payment',
            'quantity' => 1,
        ];
        
        $paymongoCheckoutData->description = 'Internet Payment';
        $paymongoCheckoutData->reference_number = $paymentTokenData->token;

        $paymongoCheckoutData->success_url = $this->buildHttpLink($paymentTokenData->token, self::REDIRECT_SUCCESS);
        $paymongoCheckoutData->cancel_url = $this->buildHttpLink($paymentTokenData->token, self::REDIRECT_FAILED);
        
        return $paymongoCheckoutData;
    }

    protected function buildHttpLink(string $token, string $action){
        return $this->pluginData->pluginPublicUrl.'?'.http_build_query([
            'action' => $action,
            '_token' => $token
        ]);
    }

}