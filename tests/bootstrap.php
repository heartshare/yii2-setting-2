<?php

define('YII_DEBUG', true);
require('../vendor/yiisoft/yii2/Yii.php');
require('../vendor/autoload.php');

$config = [
    'id' => 'cszchen/yii2-setting',
    'basePath' => dirname(__DIR__),
    'components' => [
        
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=enterprise',
            'username' => 'root',
            'password' => '123456',
            'charset' => 'utf8',
            'tablePrefix' => 'ent_'
        ]
    ]
];

$app = new \yii\console\Application($config);
