<?php

namespace Sweeeeep\PaymongoUcrm\Facade;

use Sweeeeep\PaymongoUcrm\Services\OptionManager;

abstract class AbstractPaymongoService {

    protected $secretKey;

    protected $httpClient;
    
    protected $optionManager;

    const BASE_URI = 'https://api.paymongo.com';

    const API_VERSION = 'v1';

    public function __construct(OptionManager $optionManager)
    {
        $this->optionManager = $optionManager->load();
        $this->httpClient = new \GuzzleHttp\Client([
            'base_uri' => self::BASE_URI . "/" . self::API_VERSION . "/",
            'auth' => [
                $this->optionManager->isSandboxMode ? $this->optionManager->paymongoLiveSecretKey : $this->optionManager->paymongoTestSecretKey, ''
            ]
        ]);
    }

    public function command(string $endpoint, string $method, array $data = []){
        try{
            $request = $this->httpClient->request($method, $endpoint, [
                'data' => [
                    'attributes' => $data
                ]
            ]);
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
        return json_decode((string) $request->getBody(), true);
    }
    
}