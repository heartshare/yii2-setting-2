<?php

namespace cszchen\setting\actions;

use cszchen\setting\models\Definition;
use cszchen\setting\models\SettingForm;
use yii\base\Action;

class SettingAction extends Action
{
    public $fields = [];

    public function run()
    {

    }

    protected function generateModel()
    {
        $settingForm = new SettingForm();
        foreach ($this->fields as $field) {
            list($section, $key) = explode('.', $field, 2);
            if ($section && $key) {
                if ($key == '*') {
                    $condition = ['section' => $section];
                } else {
                    $condition = ['section' => $section, 'key' => $key];
                }
                $definitions = Definition::findAll($condition);
            }
        }
        return $settingForm;
    }

    protected function setModel(SettingForm $model, Definition $definitions)
    {
        if (!$definitions) {
            return ;
        }
        foreach ($definitions as $definition) {
            $attr = $definition->section . '_sk_' . $definition->key;
            //$value = Yii::$app->setting->get()
            //$model->
        }
    }
}
