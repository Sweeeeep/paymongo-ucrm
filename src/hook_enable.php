<?php
declare(strict_types=1);

use Sweeeeep\PaymongoUcrm\Services\OptionManager;
use Ubnt\UcrmPluginSdk\Service\PluginConfigManager;
use Ubnt\UcrmPluginSdk\Service\UcrmOptionsManager;

require_once __DIR__ . '/vendor/autoload.php';



$pluginConfigManager = PluginConfigManager::create();
$config = $pluginConfigManager->loadConfig();
$optionsManager = UcrmOptionsManager::create();
$options = $optionsManager->loadOptions();

$log = \Ubnt\UcrmPluginSdk\Service\PluginLogManager::create();

$data = [
    'data' => [
        'attributes' => [
            'url' => $options->pluginPublicUrl . '?' . http_build_query(['action' => 'paymongoWebhook', 'timestamp' => time()]),
            'events' => ['checkout_session.payment.paid']
        ]
    ]
];

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'https://api.paymongo.com/v1/webhooks');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$headers = array();
$headers[] = 'Accept: application/json';
$headers[] = 'Authorization: Basic ' . base64_encode($config['paymongoTestSecretKey']);
$headers[] = 'Content-Type: application/json';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);
$arrResult = json_decode($result, true);

if(!isset($arrResult['errors'])){
    file_put_contents('webhook.json', json_encode([
        'webhookId' => $arrResult['data']['id'],
        'webhookSecretKey' => $arrResult['data']['attributes']['secret_key']
    ]));
}

$log->appendLog($result);

if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}
curl_close($ch);