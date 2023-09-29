<?php

declare(strict_types=1);

namespace Sweeeeep\PaymongoUcrm\Factory;

use Exception;
use Sweeeeep\PaymongoUcrm\Data\PaymentTokenData;
use Ubnt\UcrmPluginSdk\Service\UcrmApi;

class PaymentTokenDataFactory{

    private $ucrmApi;

    public function __construct(UcrmApi $ucrmApi) {
        $this->ucrmApi = $ucrmApi;
    }

    public function getObject($jsonData) : PaymentTokenData {

        $request = $this->ucrmApi->get("payment-tokens/{$jsonData['_token']}");
        $client = $this->ucrmApi->get("clients/{$request['clientId']}");

        if($request['invoideId'] != null && $request['amount'] == null){
            $invoice = $this->ucrmApi->get("invoices/{$request['invoiceId']}");
            $request['amount'] = $invoice['amountToPay'];
        }

        $responses = array_merge(
            $request, 
            $client,
        );

        $paymentTokenData = new PaymentTokenData();
        $paymentTokenDataClass = new \ReflectionClass($paymentTokenData);
        foreach($paymentTokenDataClass->getProperties() as $paymentTokenProperties){
            if(array_key_exists($paymentTokenProperties->getName(), $responses)){
                $paymentTokenProperties->setValue($paymentTokenData, $responses[$paymentTokenProperties->getName()]);
            }
        }

        return $paymentTokenData;
    }

}