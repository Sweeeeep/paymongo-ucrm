<?php
declare(strict_types=1);

use Sweeeeep\PaymongoUcrm\Services\OptionManager;
use Ubnt\UcrmPluginSdk\Service\PluginConfigManager;

require_once __DIR__ . '/vendor/autoload.php';



$pluginConfigManager = PluginConfigManager::create();
$config = $pluginConfigManager->loadConfig();

$log = \Ubnt\UcrmPluginSdk\Service\PluginLogManager::create();
$log->appendLog($config['paymongoTestSecretKey']);
