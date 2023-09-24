<?php

namespace Sweeeeep\PaymongoUcrm;

class Plugin {

    public function __construct(){

    }

    public function run(){
    }

    public function processHttpRequest(){
        // process client request from crm and redirect checkout link
    }

    public function processWebhookRequest(){
        // validate if request is legit form paymongo server
    }

    public function processPluginHooks($type){
        switch ($type) {
            case 'configure':

                break;
            case 'enable':

                break;
            case 'disable':

                break;
            default:
                die('Unauthorized');
                break;
        }

    }

}