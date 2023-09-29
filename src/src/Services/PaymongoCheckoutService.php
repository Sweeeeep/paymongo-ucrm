<?php 

namespace Sweeeeep\PaymongoUcrm\Services;

use Sweeeeep\PaymongoUcrm\Facade\AbstractPaymongoService;

class PaymongoCheckoutService extends AbstractPaymongoService{

    const URI = '/checkout_sessions';

    public function __construct(OptionManager $optionManager){
        parent::__construct($optionManager);
    }
    
    public function create($params){
        return $this->httpClient->request('POST', self::URI, $params);
    }

}