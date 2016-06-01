# yii2-setting  
a setting module for yii2.  

Basic Useage
============

config:  
```php
return [
    /*
    ... 
    */
    "components" => [
        "setting" => "cszchen\setting\Setting"
    ]
];
```

set with section:  
```php
Yii::$app->setting->set('package.name', 'cszchen/setting');
Yii::$app->setting->set('author', 'cszchen <me@csz.link>', 'package');
```
get the setting;  

```php
Yii::$app->setting->get('package.name'); //cszchen/setting
Yii::$app->setting->get('package.*'); //array('name' => 'cszchen/setting', 'author' => 'cszchen <me@csz.link>') 
```

you can also set without section:  
```php
Yii::$app->setting("date", date("Y"));
```

Advance Usage
===