<?php

namespace cszchen\setting\actions;

use cszchen\setting\models\Definition;
use yii\base\Action;

class DefinitionAction extends Action
{
    public $settings = [];

    public $viewPath = '@cszchen/setting/views/definition.php';

    //public $modelClass = 'cszchen\setting\models\Definition';

    public function run()
    {
        $model = new Definition();
        return $this->controller->render($this->viewPath, ['model' => $model]);
    }
}
