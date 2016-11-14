<?php
require_once 'helper.php';

$apiKey = '852af44bafef22e96d8277f3227f0998';
//$sessionId = '{{session ID}}';
$sessionId = 'a4490d430ad0e4ae1de133e9ebec7cb0';
$helper = new DDeliveryHelper($apiKey, true);

print_r($helper->getOrder($sessionId));
$params = [
    'session' => $sessionId,
    'to_name' => 'John Doe',
    'to_phone' => '+70939813447',
    'shop_refnum' => '124',
    'to_email' => 'demo@email.ru'
];
print_r($helper->editOrder($sessionId, $params));
$params = [
    'session' => $sessionId,
    'to_name' => 'John Doe',
    'to_phone' => '+70939813447',
    'shop_refnum' => '124',
    'to_email' => 'demo@email.ru'
];
print_r($helper->sendOrder($sessionId, $params));
