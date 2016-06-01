<?php

namespace cszchen\setting;

use yii;
use yii\base\Component;
use yii\helpers\ArrayHelper;

class Setting extends Component
{
    public $cache = 'cache';

    public $cacheKey = 'cszche/setting/cache';

    public $storages = [];

    public function init()
    {
        parent::init();
        foreach ($this->storages as $name => $storage) {
            if (!$storage instanceof Storage) {
                $this->storages[$name] = Yii::createObject($storage);
            }
        }
        if (!$this->storages) {
            $this->storages['db'] = Yii::createObject(DbStorage::className());
        }
    }

    public function get($key, $default = '')
    {
        list($section, $key) = $this->formatKey($key, null);
        $data = $this->getData();
        if ($key == '*') {
            return isset($data[$section]) ? $data[$section] : [];
        } else {
            return $data && isset($data[$section][$key]) ? $data[$section][$key] : $default;
        }
    }

    /**
     * store the setting
     * @param $key
     * @param $value
     * @return boolean
     */
    public function set($key, $value, $section = null)
    {
        list($section, $key) = $this->formatKey($key, $section);
        $success = false;
        $storages = $this->getStorages($section);
        if (!$storages) {
            return false;
        }
        foreach ($this->getStorages($section) as $storage) {
            $success = $success || $storage->save($section, $key, $value);
        }
        $this->clearCache();
        return $success;
    }

    /**
     * Store a section settings
     * @param $section
     * @param $value
     * @return bool
     */
    public function setSection($section, $value)
    {
        $success = true;
        foreach ($value as $key => $value) {
            $success = $success && $this->set($key, $value, $section);
        }
        return $success;
    }

    /**
     * check the key exists.
     * @param $key
     * @return bool
     */
    public function has($key)
    {
        list($section, $key) = $this->formatKey($key, null);
        $data = $this->getData();
        return $data && isset($data[$section][$key]);
    }

    public function clearCache()
    {
        $cache = $this->getCache();
        if ($cache) {
            $cache->delete($this->cacheKey);
        }
    }

    public function getData($noCache = false)
    {
        if (!$noCache && $this->getCache() && ($data = $this->getCache()->get($this->cacheKey)) != false) {
            return $data;
        }
        $data = [];
        foreach ($this->storages as $storage) {
            $data = array_merge($data, $storage->getData());
        }
        if ($data && $this->getCache()) {
            $this->getCache()->set($this->cacheKey, $data);
        }
        return $data;
    }

    protected function getCache()
    {
        if (Yii::$app->has($this->cache)) {
            return Yii::$app->get($this->cache);
        }
        return false;
    }

    protected function formatKey($key, $section)
    {
        if ($section === null) {
            $part = explode('.', $key, 2);
            if (count($part) >= 2) {
                $section = $part[0];
                $key = $part[1];
            } else {
                $section = '';
            }
        }
        return [$section, $key];
    }

    protected function getStorages($section)
    {
        $storages = [];
        foreach ($this->storages as $name => $storage)
        {
            if (!$storage->sections || in_array($section, $storage->sections)) {
                $storages[$name] = $storage;
            }
        }
        return $storages;
    }
}
