<?php

namespace cszchen\setting\models;

use cszchen\setting\types\Type;
use yii\db\ActiveRecord;

class Definition extends ActiveRecord
{
    public static function tableName()
    {
        return "{{%setting_definition}}";
    }

    public function rules()
    {
        return [
            [['section', 'key', 'required', 'type'], 'required'],
            [['section', 'key'], 'string', 'max' => 255],
            ['required', 'boolean'],
            ['type', 'in', 'range' => Type::typeNames()],
        ];
    }
}
