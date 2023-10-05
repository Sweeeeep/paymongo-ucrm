<?php

namespace Sweeeeep\PaymongoUcrm;

use Illuminate\Http\Request;
use Sweeeeep\PaymongoUcrm\Factory\PaymentTokenDataFactory;
use Sweeeeep\PaymongoUcrm\Factory\PaymongoCheckoutFactory;
use Sweeeeep\PaymongoUcrm\Services\OptionManager;
use Sweeeeep\PaymongoUcrm\Services\PaymongoCheckoutService;
use Sweeeeep\PaymongoUcrm\Services\PaymongoWebhookService;
use Ubnt\UcrmPluginSdk\Service\PluginLogManager;

class Plugin {

    const PG_WH_PAID = '';
    const PG_WH_FAILED = '';

    private $pluginData;
    private $paymentTokenDataFactory;
    private $paymongoCheckoutService;
    private $paymongoCheckoutFactory;
    private $paymongoWebhookService;
    private $pluginLogManager;

    public function __construct(
        OptionManager $optionManager,
        PaymentTokenDataFactory $paymentTokenDataFactory,
        PaymongoCheckoutService $paymongoCheckoutService,
        PaymongoCheckoutFactory $paymongoCheckoutFactory,
        PaymongoWebhookService $paymongoWebhookService,
        PluginLogManager $pluginLogManager
        
    ){
        $this->pluginData = $optionManager->load();
        $this->paymentTokenDataFactory = $paymentTokenDataFactory;
        $this->paymongoCheckoutService = $paymongoCheckoutService;
        $this->paymongoCheckoutFactory = $paymongoCheckoutFactory;
        $this->paymongoWebhookService = $paymongoWebhookService;
        $this->pluginLogManager = $pluginLogManager;
    }

    public function run(Request $request){
        if($request->has('_token')){
            return $this->processHttpRequest($request);
        }
        $this->processWebhookRequest($request);
    }

    public function processHttpRequest(Request $request){
        $client = $this->paymentTokenDataFactory->getObject([
            '_token' => $request->external_id
        ]);

        $paymongoCheckoutData = $this->paymongoCheckoutFactory->getParsedObject($client);
        $paymongoCheckout = $this->paymongoCheckoutService->create($paymongoCheckoutData);

        header("Location: {$paymongoCheckout['data']['checkout_url']}");
    }

    public function processWebhookRequest(Request $request){
        $paymongoSignatureHeader = $request->header('Paymongo-Signature');
        $webhookSecretKey = $this->pluginData->webhookSecretKey;

        list($timestamp, $testModeSignature, $liveModeSignature) = explode(',', $paymongoSignatureHeader);
        list($timestampKey, $timestampValue) = explode('=', $timestamp);        
        list($testSignatureKey, $testSignatureValue) = explode('=', $testModeSignature);
        list($liveSignatureKey, $liveSignatureValue) = explode('=', $testModeSignature);

        $calculatedSignature = hash_hmac('sha256', $timestampValue . '.' . $request->getContent(), $webhookSecretKey);

        if($calculatedSignature == ($testSignatureValue == null ? $liveSignatureValue : $testSignatureValue)){
            $this->pluginLogManager->appendLog($paymongoSignatureHeader);
            $this->pluginLogManager->appendLog($request->getContent());
        }else{
            $this->pluginLogManager->appendLog('Invalid Signature Header: [Response Header'. $paymongoSignatureHeader . ']');
        }
    }

    public function processPluginHooks($type){
        switch ($type) {
            case 'enable':
                $this->pluginLogManager->appendLog('Hook Enable');
                return $this->paymongoWebhookService->enableWebhook();
                break;
            case 'disable':
                $this->pluginLogManager->appendLog('Hook Disable');
                return $this->paymongoWebhookService->disableWebhook();
                break;
            default:
                die('Unauthorized');
                break;
        }

    }

}