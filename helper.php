<?php

class DDeliveryHelper
{

    const DEV_URL = 'http://sdk2.dev.ddelivery.ru/api/v1/integration/';
    const PROD_URL = 'http://sdk.ddelivery.ru/api/v1/integration/';

    public $apiKey;
    public $isTest;

    public function __construct($apiKey = null, $isTest = false)
    {
        $this->apiKey = $apiKey;
        $this->isTest = $isTest;
    }

    /**
     * @param $urlSuf
     * @param $session
     * @param array $params
     * @return array
     */
    private function request($urlSuf, $session, $params = [])
    {
        $params['session'] = $session;
        $url = $this->isTest ? self::DEV_URL : self::PROD_URL;
        $url .= $urlSuf;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url . '?' . http_build_query($params)); // set url to post to
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);// allow redirects
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return into a variable
        $result = json_decode(curl_exec($ch), true); // run the whole process
        curl_close($ch);
        return $result;
    }

    /**
     *
     * Получение информации о заказе
     *
     * @param $session
     * @return array
     */
    public function getOrder($session)
    {
        return $this->request('order.json', $session);
    }

    /**
     *
     * Редактирование макета заказа
     *
     * @param $session
     * @param array $params
     * to_name - client name,
     * to_phone - client phone,
     * to_email - client email,
     * to_flat - client flat,
     * to_street - client street
     * to_house - client house
     * comment - client comment
     * payment_variant - CMS id
     * local_status - CMS status id
     * shop_refnum - CMS order ID,
     * payment_price - sum
     * @return array
     *
     * @throws Exception
     */
    public function editOrder($session, $params)
    {
        if(!$this->apiKey){
            throw new Exception('No API - key defined');
        }
        return $this->request($this->apiKey . '/order-edit.json', $session, $params);
    }

    /**
     *
     * Отправка заявки на DDelivery.ru
     *
     * @param $session
     * @param array $params
     * to_name - client name,
     * to_phone - client phone,
     * to_email - client email,
     * to_flat - client flat,
     * to_street - client street
     * to_house - client house
     * comment - client comment
     * payment_variant - CMS id
     * local_status - CMS status id
     * shop_refnum - CMS order ID,
     * payment_price - sum
     * @return array
     * @throws Exception
     */
    public function sendOrder($session, $params)
    {
        if(!$this->apiKey){
            throw new Exception('No API - key defined');
        }
        return $this->request($this->apiKey . '/order-send.json', $session, $params);
    }

}