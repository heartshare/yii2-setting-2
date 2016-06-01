<?php

namespace cszchen\setting;

use yii\base\Component;

abstract class Storage extends Component
{
    public $sections = [];

    public function save($section, $key, $value)
    {
        if (!$this->sections || in_array($section, $this->sections)) {
            return $this->store($section, $key, $value);
        }
    }

    abstract public function getData();

    abstract protected function store($section, $key, $value);
}
