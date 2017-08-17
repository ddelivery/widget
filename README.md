Виджет DDelivery 
=================================================
Виджет DDelivery для быстрой интеграции калькулятора стоимости доставки на web - приложение.
Удобство данного подхода по сравнению с предыдущими версиями виджета в том, что в данной 
реализации минимизированы трудозатраты на серверной стороне CMS. Виджет управляется через API-ключ магазина созданного
в Личном кабинете DDelivery.

Назначение 
----------
1. Расчет стоимости доставки при оформлении заказа
2. Проверка расчета стоимости заказа по сессии оформления заказа (получается через javascript callback-вызов, 
во время расчета доставки виджетом)
3. Отправка заказа на сервер DDelivery.ru

Инсталляция
----------
Пример работающего виджета можно посмотреть на примере в файле index.html этого ресурса.
Виджет ведет расчет доставки для определенного магазина. Для начала работы виджета необходимо его 
активировать в Личном кабинете в разделе Магазины. 

Также магазин можно активировать через API. Для этого необходимо выполнить следующие запросы:
1. Включить функционал виджета
https://sdk.ddelivery.ru/api/v1/integration/{shop_api_key}/link.json
```
{"success":1,"data":{"id":74022,"message":"Shop has linked successful"}}
```
id - идентификатор по которому подключается виджет и ассоциируется с магазином
2. Выключить функционал виджета
https://sdk.ddelivery.ru/api/v1/integration/{shop_api_key}/reset.json
```
{"success":1,"data":[]}
```
3. Войти в настройки виджета (ПАМ)
https://sdk.ddelivery.ru/api/v1/integration/{shop_api_key}/pam.json
```
{"success":1,"data":{"url":"https://sdk.ddelivery.ru/settings/93d3a55056a1a6f3a026b218839b74fb/auth.json"}}
```
url - url для входа в ПАМ

Для начало работы необходимо подключить js-файл к странице сайта:
```
<script type="text/javascript" src="//sdk.ddelivery.ru/assets/ddelivery.js"></script>
```

Назначается определенный элемент в в котором произойдет инициализация виджета:
```
<div id="widget"></div>
```
Далее необходимо заполнить js-объект со списком товаров в корзине. Из этого списка товаров виджет  
автоматически пересчитает габариты товаров для расчета доставки:
```
var products = [
        {
            id: '122', // ID товара в CMS
            name: 'Some piece of products', // Наименование товаров
            price: 524, // Цена товара
            width: 10, // ширина
            height: 10, высота
            length: 0, // длина
            weight: 1, // вес
            quantity: 2, // количество единиц товара
            sku: 'SKU PRODUCT' // артикул товара
        },
        {
            id: '123',
            name: 'Some piece of products',
            price: 524,
            width: 10,
            height: 10,
            length: 10,
            weight: 1,
            quantity: 2,
            sku: 'SKU PRODUCT'
        }
    ];
```
Инициализация виджета происходит вызовом метода:
```
DDeliveryWidget.init(elementId, params, callbacks);
```
elementId - id html элемента на странице, в котором пройдет инициализация виджета.
params - список параметров инициализации. 

callbacks - javascript объект с методами-обработчиками событий, которые будут вызываться при изменении цены 
доставки, или при выборе пользователем определенного способа доставки, для получения 
информации о выбранном способе доставки.

### Свойства params:
products - объект с товарами из корзины
id - идентификатор магазина DDelivery(смотреть в Личном кабинете)
width - ширина виджета(iframe в котором инициализируется виджет)
height - высота виджета
env - среда работы виджета(DDeliveryWidget.ENV_PROD|DDeliveryWidget.ENV_DEV), для отладки используется среда DEV,
Среда dev использует api-сервер dev.ddelivery.ru, среда prod использует api-сервер cabinet.ddelivery.ru
### Свойства callbacks
price - метод для получения текущей цены доставки из виджета. Этот метод позволяет отбражать цену в CMS в live режиме
change - метод который вызывается при подтверждении пользователем способа доставки в виджете.
Пример метода для инициализации модуля:
```
DDeliveryWidget.init('widget', {
        products: products,
        id: 201,
        width: 500,
        height: 550,
        env: DDeliveryWidget.ENV_DEV
    }, {
        price: function (data) {
            //console.log('price');
            console.log(data);
        },
        change: function (data) {
            //console.log('change');
            console.log(data);
        }
    });
```
Идентификатор сессии будет передан в метод change, в параметре client_token.

Валидация выбора доставки в виджете, на странице оформления заказа в CMS  
------------------------------------------------------------------------
На странице оформления заказа необходимо получать информацию о том, выбрал ли пользователь определенный
способ доставки в виджете или нет. Исходя из этой информации можно разрешать пользователю подтверждать 
оформление заказа в CMS или выводить ему сообщение про ошибку. Вся обработка идет на js.
Есть метод DDeliveryWidget.validate() который возвращает true если в виджете выбран способ доставки.
Если способ доставки не выбран, или в виджете есть какая-то другая ошибка, то ее текст можно получить через метод
DDeliveryWidget.getErrorMsg().
Пример:
```
button.onclick = function () {
        if (DDeliveryWidget.validate()) {
            alert("send order");
        } else {
            alert(DDeliveryWidget.getErrorMsg());
        }
    }
```
Проверка цены доставки на серверной стороне
-------------------------------------------
Примеры запросов на серверную часть можно посмотреть на примере файла example.php этого ресурса.
Правильность цены можно проверять на серверной стороне. Для этого необходимо знать идентификатор сессии оформления 
заказа, который можно получить в методе change(см. предыдущий раздел). Для этого можно использовать php-класс 
DDeliveryHelper.
В конструкторе принимается 2 параметра:
1. API - ключ магазина DDelivery(используется для отправки заказа)
2. Флаг тестового режима (true - тестовый API-сервер, используется сервер dev.ddelivery.ru, 
false - API-сервер cabinet.ddelivery.ru)

### Пример проверки стоимости доставки: 
```
$sessionId = '{{sessionId}}';
$helper = new DDeliveryHelper();
$helper->getOrder($sessionId);
```

### Пример ответа
```
Array
(
    [success] => 1
    [data] => Array
        (
            [id] => 3258 
            [client_price] => 266.38 // цена доставки
            [company] => 115 //компания доставки
            [company_name] => DPD E-parcel 
            [point] => //идентификатор точки
            [type] => 2 //тип доставки
            [to_name] => //имя клиента
            [to_phone] => //телефон клиента
            [to_email] => //email клиента
            [to_flat] => xxxxxxxxx //квартира
            [to_street] => xxx //улица
            [to_house] => xxxxxxxxxxxxxx //дом
            [city_name] => Москва //название города
            [ddelivery_id] => //ID заявки на сервере DD (после отправки заказа)
            [city] => 151184 //ID города
            [info] => Курьерская доставка, xxx, xxxxxxxxxxxxxx, xxxxxxxxx, компания: DPD E-parcel, Москва // краткая информация о способе доставки
            [company_info] => Array
                (
                    [type] => 2
                    [indexTC] => 1
                    [date_due] => 2
                    [index_tc] => 1069.52
                    [is_custom] => 
                    [items_count] => 1
                    [pickup_date] => 14.11.2016
                    [total_price] => 516.38
                    [client_price] => 266.38
                    [company_type] => 2
                    [confirm_date] => 13.11.2016 23:45
                    [packing_paid] => 
                    [payment_time] => 0
                    [pickup_price] => 250
                    [return_price] => 0
                    [delivery_date] => 15.11.2016
                    [packing_price] => 
                    [sorting_price] => 15
                    [delivery_price] => 240.9
                    [packing_message] => 
                    [delivery_company] => 115
                    [packing_required] => 
                    [delivery_time_avg] => 1
                    [delivery_time_max] => 1
                    [delivery_time_min] => 1
                    [payment_price_fee] => 0
                    [declared_price_fee] => 10.48
                    [return_client_price] => 0
                    [payment_availability] => 1
                    [return_partial_price] => 0
                    [delivery_company_name] => DPD E-parcel
                    [pickup_company_driver_version] => 0
                    [delivery_company_driver_version] => 15
                )

            [cart] => Array
                (
                    [0] => Array
                        (
                            [id] => 122
                            [sku] => SKU PRODUCT
                            [name] => Some piece of products
                            [price] => 524
                            [width] => 10
                            [height] => 10
                            [length] => 0
                            [weight] => 1
                            [quantity] => 2
                        )

                    [1] => Array
                        (
                            [id] => 123
                            [sku] => SKU PRODUCT
                            [name] => Some piece of products
                            [price] => 524
                            [width] => 10
                            [height] => 10
                            [length] => 10
                            [weight] => 1
                            [quantity] => 2
                        )

                )

            [payment_availability] => 1 //возможность наложенного платежа для вібранной компании
            [packing_price] => 
            [packing_required] => 
            [packing_message] => 
            [packing_paid] => 
            [pickup_warehouse] => 
            [order_id] => 3258
            [comment] => // Комментарий к заказу
            [payment_variant] => // способ оплаты в CMS
            [local_status] => // статус в CMS 
            [shop_refnum] => // ID заказа в CMS
            [payment_price] => // сумма наложенного платежа
            [npp_option] => // Признак наложенного платежа (0|1)
        )

)
```

Отправка заказа на сервер DDelivery.ru
--------------------------------------
Для отправки заказов на сервер необходимо иметь API-ключ магазина.
При отправке используются дополнительные параметры которые требует API.
Для выставления промежуточных значений параметров, также, можно использовать дополнительный метод для редактирования заказа.

### Метод для редактирования заказа
Для редактирования заказа можно использовать php-класс DDeliveryHelper.
```
$sessionId = '4ae5998a753861c4e2513b7364119c4f';
$apiKey = '852af44bafef22e96d8277f3227f0998';
$helper = new DDeliveryHelper($apiKey);

$params = [
    'session' => $sessionId,
    'to_name' => 'John Doe',
    'to_phone' => '+70939813447',
    'shop_refnum' => '124',
    'to_email' => 'demo@email.ru'
    'npp_option' => 1 (признак наложенного платежа. 0 (или можно не передавать эту опцию) - не учитывать, 1 - учитывать)
];
$helper->editOrder($sessionId, $params);
```

session - Идентификатор сессии который вернул виджет через обратный вызов (js-метод change)
to_name - Имя клиента
to_phone - Телефон клиента
shop_refnum - Идентификатор заказа в CMS
to_email - Email клиента

Пример ответа
```
Array
(
    [success] => 1
    [data] => Array
        (
            [id] => 3259
            [client_price] => 266.38
            [company] => 115
            [company_name] => DPD E-parcel
            [point] => 
            [type] => 2
            [to_name] => John Doe
            [to_phone] => +70939813447
            [to_email] => demo@email.ru
            [to_flat] => xxxxxxxxxxxxxx
            [to_street] => xxxx
            [to_house] => xxxxxxxx
            [city_name] => Москва
            [ddelivery_id] => 
            [city] => 151184
            [info] => Курьерская доставка, xxxx, xxxxxxxx, xxxxxxxxxxxxxx, компания: DPD E-parcel, Москва
            [company_info] => Array
                (
                    [type] => 2
                    [indexTC] => 1
                    [date_due] => 2
                    [index_tc] => 1069.52
                    [is_custom] => 
                    [items_count] => 1
                    [pickup_date] => 15.11.2016
                    [total_price] => 516.38
                    [client_price] => 266.38
                    [company_type] => 2
                    [confirm_date] => 14.11.2016 23:45
                    [packing_paid] => 
                    [payment_time] => 0
                    [pickup_price] => 250
                    [return_price] => 0
                    [delivery_date] => 16.11.2016
                    [packing_price] => 
                    [sorting_price] => 15
                    [delivery_price] => 240.9
                    [packing_message] => 
                    [delivery_company] => 115
                    [packing_required] => 
                    [delivery_time_avg] => 1
                    [delivery_time_max] => 1
                    [delivery_time_min] => 1
                    [payment_price_fee] => 0
                    [declared_price_fee] => 10.48
                    [return_client_price] => 0
                    [payment_availability] => 1
                    [return_partial_price] => 0
                    [delivery_company_name] => DPD E-parcel
                    [pickup_company_driver_version] => 0
                    [delivery_company_driver_version] => 15
                )

            [cart] => Array
                (
                    [0] => Array
                        (
                            [id] => 122
                            [sku] => SKU PRODUCT
                            [name] => Some piece of products
                            [price] => 524
                            [width] => 10
                            [height] => 10
                            [length] => 0
                            [weight] => 1
                            [quantity] => 2
                        )

                    [1] => Array
                        (
                            [id] => 123
                            [sku] => SKU PRODUCT
                            [name] => Some piece of products
                            [price] => 524
                            [width] => 10
                            [height] => 10
                            [length] => 10
                            [weight] => 1
                            [quantity] => 2
                        )

                )

            [payment_availability] => 1
            [packing_price] => 
            [packing_required] => 
            [packing_message] => 
            [packing_paid] => 
            [pickup_warehouse] => 
            [order_id] => 3259
            [comment] => 
            [payment_variant] => 
            [local_status] => 
            [shop_refnum] => 124
            [payment_price] =>
            [npp_option] => // Признак наложенного платежа (0|1)
        )

)
```


### Метод для отправки заказа
```
$params = [
        'session' => $sessionId,
        'to_name' => 'John Doe',
        'to_phone' => '+70939813447',
        'shop_refnum' => '124',
        'to_email' => 'demo@email.ru'
        'npp_option' => 1 (признак наложенного платежа. 0 (или можно не передавать эту опцию) - не учитывать, 1 - учитывать)
];
print_r($helper->sendOrder($sessionId, $params));
```
Пример ответа
```
Array
(
    [success] => 1
    [data] => Array
        (
            [id] => 3259
            [client_price] => 266.38
            [company] => 115
            [company_name] => DPD E-parcel
            [point] => 
            [type] => 2
            [to_name] => John Doe
            [to_phone] => +70939813447
            [to_email] => demo@email.ru
            [to_flat] => xxxxxxxxxxxxxx
            [to_street] => xxxx
            [to_house] => xxxxxxxx
            [city_name] => Москва
            [ddelivery_id] => 188960
            [city] => 151184
            [info] => Курьерская доставка, xxxx, xxxxxxxx, xxxxxxxxxxxxxx, компания: DPD E-parcel, Москва
            [company_info] => Array
                (
                    [type] => 2
                    [indexTC] => 1
                    [date_due] => 2
                    [index_tc] => 1069.52
                    [is_custom] => 
                    [items_count] => 1
                    [pickup_date] => 15.11.2016
                    [total_price] => 516.38
                    [client_price] => 266.38
                    [company_type] => 2
                    [confirm_date] => 14.11.2016 23:45
                    [packing_paid] => 
                    [payment_time] => 0
                    [pickup_price] => 250
                    [return_price] => 0
                    [delivery_date] => 16.11.2016
                    [packing_price] => 
                    [sorting_price] => 15
                    [delivery_price] => 240.9
                    [packing_message] => 
                    [delivery_company] => 115
                    [packing_required] => 
                    [delivery_time_avg] => 1
                    [delivery_time_max] => 1
                    [delivery_time_min] => 1
                    [payment_price_fee] => 0
                    [declared_price_fee] => 10.48
                    [return_client_price] => 0
                    [payment_availability] => 1
                    [return_partial_price] => 0
                    [delivery_company_name] => DPD E-parcel
                    [pickup_company_driver_version] => 0
                    [delivery_company_driver_version] => 15
                )

            [cart] => Array
                (
                    [0] => Array
                        (
                            [id] => 122
                            [sku] => SKU PRODUCT
                            [name] => Some piece of products
                            [price] => 524
                            [width] => 10
                            [height] => 10
                            [length] => 0
                            [weight] => 1
                            [quantity] => 2
                        )

                    [1] => Array
                        (
                            [id] => 123
                            [sku] => SKU PRODUCT
                            [name] => Some piece of products
                            [price] => 524
                            [width] => 10
                            [height] => 10
                            [length] => 10
                            [weight] => 1
                            [quantity] => 2
                        )

                )

            [payment_availability] => 1
            [packing_price] => 
            [packing_required] => 
            [packing_message] => 
            [packing_paid] => 
            [pickup_warehouse] => 
            [order_id] => 3259
            [comment] => 
            [payment_variant] => 
            [local_status] => 
            [shop_refnum] => 124
            [payment_price] =>
            [npp_option] => // Признак наложенного платежа (0|1)
        )

)
```
Выставляется параметр ddelivery_id - идентификатор заказа на стороне DDelivery.ru.
Повторно отправлять заказ невозможно, редактировать отправленный заказа нельзя. 