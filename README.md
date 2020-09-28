# laravel项目常量库

用于方便管理和调用数据库常量。

### 安装方法
```shell
    composer require urland/laravel-constant
    php artisan vendor:publish --tag=urland-constant
```

### 使用说明

resources/constant/demo.php
```php
    return [
        'pay' => [
            'type' => [
                'alipay' => '支付宝',
                'wechat' => '微信支付',
            ],
        ],
    ];
```

1. 获取常量值
```php
    $payTypeKey = 'wechat';
    $payTypeId = cons('pay.type.' . $payTypeKey);
    // $payTypeId == 2
```

2. 根据常量值获取key
```php
    $payTypeId = 2;
    $payTypeKey = cons()->key('pay.type', $payTypeId);
    // $payTypeKey == 'wechat'

    // 获取 id => key 方式的数组
    $payTypeKeys = cons()->key('pay.type');
    /*
        $payTypeKeys == [
            1 => 'alipay',
            2 => 'wechat',
        ];
    */
```

3. 获取常量对应语言
```php
    $payTypeKey = 'wechat';
    $payTypeName = cons()->lang('pay.type.' . $payTypeKey);
    // $payTypeName == '微信支付'
```

4. 根据常量值获取对应语言
```php
    $payTypeId = 2;
    $payTypeName = cons()->valueLang('pay.type', $payTypeId);
    // $payTypeName == '微信支付'

    // 获取 id => key 方式的数组
    $payTypeNames = cons()->valueLang('pay.type');
    /*
        $payTypeKeys == [
            1 => '支付宝',
            2 => '微信支付',
        ];
    */
```