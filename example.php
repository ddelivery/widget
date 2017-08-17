<?php
require_once 'helper.php';

$apiKey = '852af44bafef22e96d8277f3227f0998';
//$sessionId = '{{session ID}}';
$sessionId = 'a4490d430ad0e4ae1de133e9ebec7cb0';

$helper = new DDeliveryHelper($apiKey, true);


// проверяем данные заказа (например перед отправкой в DDelivery)
$order = $helper->getOrder($sessionId);

echo '<pre>';
print_r($order);
echo '</pre>';

// обновляем данные
$params = [
    'session'     => $sessionId,
    'to_name'     => 'John Doe',
    'to_phone'    => '+70939813447',
    'shop_refnum' => '124',
    'to_email'    => 'demo@email.ru',
    'npp_option'  => 0, // то есть не учитываем наложенный платёж (либо можно не передавать npp_option)
];
$order = $helper->editOrder($sessionId, $params);

echo '<pre>';
print_r($order);
echo '</pre>';

// отправляем дажнные в DDelivery
$params = [
    'session'     => $sessionId,
    'to_name'     => 'John Doe',
    'to_phone'    => '+70939813447',
    'shop_refnum' => '124',
    'to_email'    => 'demo@email.ru',
    'npp_option'  => 1, // то есть учитываем наложенный платёж
];
$order = $helper->sendOrder($sessionId, $params);

echo '<pre>';
print_r($order);
echo '</pre>';