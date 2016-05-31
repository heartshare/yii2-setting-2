<?php

namespace cszchen\components;

use yii;
use yii\base\Component;

class Setting extends Component
{
    public $cache = 'cache';

    public $cacheKey = 'cszchen/setting-cache';

    public $cacheTime = 0;

    protected $modelClass = 'cszchen\setting\models\Setting';

    protected $_data = [];

    public function init()
    {
        $this->cache = Yii::$app->get($this->cache);
        if (!$this->modelClass) {
            throw new yii\base\InvalidConfigException('The "modelClass" configuration for the component is required.');
        }
        $this->model = Yii::createObject($this->modelClass);
    }

    public function get($key, $defaultValue = '')
    {
        $data = $this->getData();
        return isset($data[$key]) ? $data[$key] : $defaultValue;
    }

    public function set($key, $value, $type = null, $section = null)
    {
        if (is_null($section)) {
            $pieces = explode('.', $key, 2);
            if (count($pieces) > 1) {
                $section = $pieces[0];
                $key = $pieces[1];
            } else {
                $section = '';
            }
        }
        if (!$type) {
            $type = gettype($string);
        }
        if ($this->model->setSetting($section, $key, $value, $type)) {
            if ($this->clearCache()) {
                return true;
            }
        }
        return false;
    }

    public function clearCache()
    {
        if ($this->cache) {
            return $this->cache->delete($this->cacheKey);
        }
        return true;
    }

    public function getData()
    {
        if ($this->_data) {
            return $this->_data;
        }
        if ($this->cache ($this->_data = $this->cache->get($this->cacheKey)) != false) {
            return $this->_data;
        }
        $this->_data = $this->model->getSettings();
        if ($this->_data && $this->cache) {
            $this->cache->set($this->cacheKey, $this->_data, $this->cacheTime);
        }
        return $this->_data;
    }
}
