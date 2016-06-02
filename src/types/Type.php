<?php

namespace cszchen\setting\types;

use yii\base\Object;

abstract class Type extends Object
{
    public static function defitions()
    {
        return [
            'datetime' => [
                'format' => '',
                'validator' => 'datetime',
            ],
            //'date' => [],
            //'time' => [],
            'email' => [
                'validator' => 'email'
            ],
            'ip' => [
                'validator' => 'ip'
            ],
            'boolean' => [
                'validator' => 'boolean',
            ],
            'string' => [
                'validator' => 'string'
            ]
        ];
    }

    public static function typeNames()
    {
        $definitions = static::defitions();
        $types = array_keys($definitions);
        return array_combine($types, $types);
    }
}
