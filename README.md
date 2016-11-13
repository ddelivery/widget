Виджет DDelivery 
=================================================
Виджет DDelivery для быстрой интеграции калькулятора стоимости доставки в web-приложение.
Удобство данного подхода по сравнению с предыдущими версиями виджета в том, что в данной реализации минимизированы 
трудозатраты на серверной стороне CMS. 

Назначение 
----------
1. Расчет стоимости доставки при оформлении заказа
2. Проверка расчета стоимости заказа по сессии оформления заказа
3. Отправка заказа на сервер DDelivery.ru

Инсталяция
----------
Пример работающего виджета можно посмотреть напримере файла index.php этого ресурса.
Виджет ведет расчет доставки для определенного магазина. Для начала работы виджета необходимо его 
активировать в Личном кабинете в разделе Магазины. 

Приложение устанавливается подключением js-файла к странице сайта:
```
<script type="text/javascript" src="http://sdk2.dev.ddelivery.ru/assets/ddelivery.js"></script>
```

Выбирается определенный элемент в который пройдт инициализация виджета:
```
<div id="widget"></div>
```
Далее необходимо заполнить js-объект со списком товаров в корзине. Из этого списка товаров вижет  
автоматически пересчитает габариты товаров для расчета доставки 
```
var products = [
        {
            id: '122',
            name: 'Some piece of products',
            price: 524,
            width: 10,
            height: 10,
            length: 0,
            weight: 1,
            quantity: 2,
            sku: 'SKU PRODUCT'
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
Инициализация виджета проходит вызовом метода описанного ниже:
```
DDeliveryWidget.init(elementId, params, callbacks);
```
elementId - id элемента в котором пройдет инициализация виджета.
params - список параметров инициализации. 
callbacks - объект с методами - обработчиками событий, которые будут вызываться при изменении цены 
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
Валидация выбора доставки в виджете, на странице оформления заказа в CMS  
------------------------------------------------------------------------

Проверка цены доставки на серверной стороне
-------------------------------------------
Правильность цены можно проверять на серверной стороне. Для этого необходимо знать идентификатор сессии оформления 
заказа, который можно получить в методе change(см. предыдущий раздел). Для этого можно использовать php-класс 
DDeliveryHelper.
В конструкторе принимается 2 параметра:
1. API - ключ магазина DDelivery(используется для отправки заказа)
2. Флаг тестового режима (true - тестовый сервер, используется сервер dev.ddelivery.ru, 
false - сервер cabinet.ddelivery.ru)

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
            [client_price] => 266.38
            [company] => 115
            [company_name] => DPD E-parcel
            [point] => 
            [type] => 2
            [to_name] => 
            [to_phone] => 
            [to_email] => 
            [to_flat] => xxxxxxxxx
            [to_street] => xxx
            [to_house] => xxxxxxxxxxxxxx
            [city_name] => Москва
            [ddelivery_id] => 
            [city] => 151184
            [info] => Курьерская доставка, xxx, xxxxxxxxxxxxxx, xxxxxxxxx, компания: DPD E-parcel, Москва
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

            [payment_availability] => 1
            [packing_price] => 
            [packing_required] => 
            [packing_message] => 
            [packing_paid] => 
            [pickup_warehouse] => 
            [order_id] => 3258
            [comment] => 
            [payment_variant] => 
            [local_status] => 
            [shop_refnum] => 
            [payment_price] => 
        )

)
```

Отправка заказа на сервер DDelivery.ru
--------------------------------------

