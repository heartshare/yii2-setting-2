<?php

namespace cszchen\setting\models;

use yii\base\Model;

class SettingForm extends Model
{
    protected $_rules = [];

    public function rules()
    {
        return $this->_rules;
    }

    public function addRule($rule)
    {
        $this->_rules[] = $rule;
    }
}
