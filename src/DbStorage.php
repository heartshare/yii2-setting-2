<?php

namespace cszchen\setting;

use Yii;
use \yii\db\Query;
use yii\helpers\ArrayHelper;

class DbStorage extends Storage
{
    public $db = 'db';

    public $tableName = '{{%setting}}';

    public function getData()
    {
        $query = new Query();
        $settings = $query->from($this->tableName)->all($this->getDb());
        return ArrayHelper::map($settings, 'key', 'value', 'section');
    }

    protected function store($section, $key, $value)
    {
        $command = $this->getDb()->createCommand();
        $query = new Query();
        $condition = ['section' => $section, 'key' => $key];
        $exists = $query->from($this->tableName)->where($condition)->one($this->getDb());
        if ($exists && $exists['value'] == $value) {
            return true;
        }
        if ($exists) {
            $command->update($this->tableName, ['value' => $value], $condition);
        } else {
            $command->insert($this->tableName, compact('section', 'key', 'value'));
        }
        return $command->execute();
    }

    /**
     * @return \yii\db\Connection
     */
    protected function getDb()
    {
        return Yii::$app->get($this->db);
    }
}
