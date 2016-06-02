<?php

namespace cszchen\setting;

use yii\base\Component;

abstract class Storage extends Component
{
    public $sections = [];

    public $except = [];

    public function save($section, $key, $value)
    {
        if ($this->isMatch($section)) {
            return $this->store($section, $key, $value);
        }
    }

    protected function isMatch($section)
    {
        if ($this->except && in_array($section, $this->except)) {
            return false;
        }
        if (!$this->sections || in_array($section, $this->sections)) {
            return true;
        }
        return false;
    }

    abstract public function getData();

    abstract protected function store($section, $key, $value);
}
