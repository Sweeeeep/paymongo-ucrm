<?php

namespace Sweeeeep\PaymongoUcrm\Services;

use Sweeeeep\PaymongoUcrm\Data\PluginData;

class OptionManager {
    private const UCRM_JSON = 'ucrm.json';
    private const CONFIG_JSON = 'config/data.json';
    private const WEBHOOK_JSON = 'webhook.json';

    private $optionsData;

    public function load() : PluginData {

        if($this->optionsData){
            return $this->optionsData;
        }

        $options = array_merge(
            $this->getDataFromJson(self::UCRM_JSON),
            $this->getDataFromJson(self::CONFIG_JSON),
        );

        $this->optionsData = new PluginData();
        $reflectionClass = new \ReflectionClass($this->optionsData);
        foreach($reflectionClass->getProperties() as $reflectionProperty){
            if(array_key_exists($reflectionProperty->getName(), $options)){
                $reflectionProperty->setValue($this->optionsData, $options[$reflectionProperty->getName()]);
            }
        }

        return $this->optionsData;
    }

    private function getDataFromJson(string $filename): array {

        if (! file_exists($filename)) {
            return [];
        }

        return  json_decode(file_get_contents($filename), true);
    }

}