<?php


namespace Sweeeeep\PaymongoUcrm\Services;

use Sweeeeep\PaymongoUcrm\Facade\AbstractPaymongoService;

class PaymongoWebhookService extends AbstractPaymongoService {

    const URI = '/webhooks';

    public function __construct(OptionManager $optionManager){
        parent::__construct($optionManager);
    }
    
    public function create($params){
        return $this->httpClient->request('POST', self::URI, $params);
    }

    public function createWebhook(){
        if($this->optionManager->webhookId == null && $this->optionManager->webhookSecretKey == null){
            $response = $this->create([
                'url' => $this->optionManager->ucrmPublicUrl . '?' . http_build_query(['action' => 'paymongoWebhook']),
                'events' => ['checkout_session.payment.paid']
            ]);

            file_put_contents('webhook.json', json_encode([
                'webhookId' => $response['data']['id'],
                'webhookSecretKey' => $response['data']['attributes']['secret_key']
            ]));

            return $response;
        }
    }
    
    public function enableWebhook(){
        return $this->httpClient->request('POST', self::URI . '/' . $this->optionManager->webhookId . '/disable');
    }

    public function disableWebhook(){
        return $this->httpClient->request('POST', self::URI . '/' . $this->optionManager->webhookId . '/disable');
    }

}