<?php
require_once 'helper.php';

$apiKey = '852af44bafef22e96d8277f3227f0998';
$sessionId = '{{session ID}}';
$helper = new DDeliveryHelper($apiKey, true);

print_r($helper->getOrder($sessionId));
$params = [
    'session' => $sessionId,
    'to_name' => 'Tor',
    'to_phone' => '0939813447',
    'shop_refnum' => '124',
    'to_email' => 'xxxxxx@xxxxx.xx',
];
print_r($helper->editOrder($sessionId, $params));
$params = [
    'session' => $sessionId,
    'to_name' => 'Tox',
    'to_phone' => '0939813447',
    'shop_refnum' => '124',
    'to_email' => 'xxxxxx@xxxxx.xx',
];
print_r($helper->sendOrder($sessionId, $params));
