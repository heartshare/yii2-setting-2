<?php

namespace cszchen\yii2;

use yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\base\DynamicModel;

class Setting extends ActiveRecord
{
    CONST TYPE_STRING = 'string';
    CONST TYPE_OBJECT = 'object';
    CONST TYPE_INTEGER = 'integer';
    CONST TYPE_BOOLEAN = 'boolean';
    CONST TYPE_FLOAT = 'float';
    CONST TYPE_URL = 'url';
    CONST TYPE_EMAIL = 'email';
    CONST TYPE_IP = 'ip';
    CONST TYPE_DATE = 'date';
    CONST TYPE_DATETIME = 'datetime';

    public static function tableName()
    {
        return "{{%setting}}";
    }

    public function rules()
    {
        return [
            [['section', 'key'], 'string', 'max' => 255],
            ['key', 'unique', 'targetAttribute' => ['section', 'key']],
            ['value', 'string'],
            ['type', 'in', 'range' => $this->getTypes()],
            ['value', 'checkType'],
        ];
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className()
        ];
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        Yii::$app->setting->clearCache();
    }

    public function afterDelete()
    {
        parent::afterDelete();
        Yii::$app->setting->clearCache();
    }

    public function getSettings()
    {
        $settings = $this->find()->asArray()->all();
        return ArrayHelper::map($settings, 'key', 'value', 'section');
    }

    public function set($section, $key, $value, $type)
    {
        $model = $this->findOne(['section' => $section, 'key' => $key]);
        if (!$model) {
            $model = new static();
        }
        if (!in_array($type, $this->getTypes())) {
            throw new \InvalidArgumentException("The type \"$type\" is not support.");
        }
        $model->section = $section;
        $model->key = $key;
        $model->value = $value;
        $model->type = $type;
    }

    public function getTypes()
    {
        return [
            'string' => ['value', 'string'],
            'integer' => ['value', 'integer'],
            'boolean' => ['value', 'boolean', 'trueValue' => "1", 'falseValue' => "0", 'strict' => true],
            'float' => ['value', 'number'],
            'email' => ['value', 'email'],
            'ip' => ['value', 'ip'],
            'url' => ['value', 'url'],
            'object' => ['value', 'checkIsObject'],
        ];
    }

    public function checkIsObject($attr)
    {
        if (!is_object($this->$attr) && !is_array($this->$attr) && json_decode($this->$attr) == false) {
            $this->addError($attr, '{attribute} must be a valid JSON object');
        }
    }

    public function checkType($attr)
    {
        $rules = $this->getTypes();
        if (!array_key_exists($this->type, $rules)) {
            $this->addError($attr, 'Please select correct type');
        }
        $model = new DynamicModel(['value' => $this->value]);
        $model->addRule($rules[$this->type]);
        if (!$model->validate()) {
            $this->addError('value', $model->getFirstError('value'));
        }
    }
}
