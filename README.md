# yii2-setting  
a setting module for yii2

Storage different section with different medium.

Basic Useage
============
It will storage the setting in the database;

config:  
---
```php
return [
    /*
    ... 
    */
    "components" => [
        "db" => [
            /*
            ... 
            */
        ],
        "setting" => [
            "class" => "cszchen\setting\Setting",
            #"db" => "db",
            #"cache" => "cache"
        ]
    ]
];
```

set with section:  
---
```php
Yii::$app->setting->set('package.name', 'cszchen/setting');
```
or
```php
Yii::$app->setting->set('author', 'cszchen <me@csz.link>', 'package');
```
or
```php
Yii::$app->setting->setSection('package', [
    'type' => 'yii2-extension',
    'license' => 'MIT'
]);
Yii::$app->setting->get('package.license');//MIT
```
get the setting;  
---

```php
Yii::$app->setting->get('package.name'); //cszchen/setting
Yii::$app->setting->get('package.*'); //array('name' => 'cszchen/setting', 'author' => 'cszchen <me@csz.link>') 
```

you can also set without section:  
---
```php
Yii::$app->setting("date", date("Y"));
```

Advance Usage: Use different medium
===================================

config:
-------
```php
return [
    "component" => [
        "setting" => [
            "class" => "cszchen\\setting",
            "storages" => [
                "#1" => [
                    "class" => "cszchen\\setting\\FileStorage",
                    "filepath" => "path/to/file",
                    "sections" => ["db", "system"]
                ],
                "#2" => [
                    "class" => "cszchen\\setting\\DbStorage",
                    "sections" => ["web"]
                ]
            ]
        ]
    ]
];
```

then
```php
Yii::$app->setting->set("db.host", "localhost");//will be stored in file.
Yii::$app->setting->set("web.title", "cszchen/setting");//will be stored in database.
```