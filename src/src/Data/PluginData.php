<?php 

namespace Sweeeeep\PaymongoUcrm\Data;

class PluginData extends UcrmData{

    public $isSandboxMode;

    public $paymongoTestSecretKey;

    public $paymongoLiveSecretKey;

    public $isSemaphoreEnable;

    public $semaphoreApiKey;

    public $semaphoreSenderName;

    public $webhookId = null;

    public $webhookSecretKey = null;

}