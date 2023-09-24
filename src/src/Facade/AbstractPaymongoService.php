<?php

namespace Sweeeeep\PaymongoUcrm\Facade;

use Sweeeeep\PaymongoUcrm\Services\OptionManager;

abstract class AbstractPaymongoService {

    protected $secretKey;

    protected $client;
    
    protected $optionManager;

    public function __construct(OptionManager $optionManager)
    {
        $this->optionManager = $optionManager->load();
        $this->client = new \GuzzleHttp\Client([
            'base_uri' => 'https://api.paymongo.com/v1/',
            'auth' => [
                $this->optionManager->isPaymongoLive ? $this->optionManager->paymongoLiveSecretKey : $this->optionManager->paymongoTestSecretKey, ''
            ]
        ]);
    }

    public function command(string $endpoint, string $method, array $data){
        try{
            $request = $this->client->request($method, $endpoint, $data);
        }catch(\Exception $e){
            switch ($e->getCode()) {
                case '401':
                    throw new \Exception('Invalid Paymongo Secret Key.', 401);
                    break;
                case '400':
                    throw new \Exception('Invalid Request.', 400);
                    break;
                case '404':
                    throw new \Exception('Route not found.', 404);
                default:
                    throw new \Exception('Error', 404);
                    break;
            }
        }
        // dd(json_decode((string) $request->getBody(), true));
        return json_decode((string) $request->getBody(), true);
    }
    
    public function handleErrorResponse($body, $code, $url){
    
    }
}