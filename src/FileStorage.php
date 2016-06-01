<?php

namespace cszchen\setting;

use yii;
use yii\helpers\Json;
use yii\base\InvalidConfigException;

class FileStorage extends Storage
{
    public $filepath;

    /**
     * support 'json', 'xml', 'array'
     * @var string
     */
    public $format = 'json';
    
    public function init()
    {
        parent::init();
        if (!$this->filepath) {
            $this->filepath = Yii::getAlias('@runtime/setting' . $this->getFileExtension());
        }
    }

    public function getData()
    {
        if (!file_exists($this->filepath)) {
            return [];
        }
        switch (strtolower($this->format)) {
            case 'json':
                $content = file_get_contents($this->filepath);
                if (!$content) {
                    return [];
                }
                $data = Json::decode($content);
                break;
            case 'array':
                $data = include($this->filepath);
                break;
            default:
                throw new yii\base\NotSupportedException("Not support the fomart '{$this->format}'");
        }
        return is_array($data) ? $data : [];
    }

    protected function store($section, $key, $value)
    {
        $data = $this->getData();
        $data[$section][$key] = $value;
        if (($fp = fopen($this->filepath, 'w')) === false) {
            throw new InvalidConfigException("Unable to write to setting file: {$this->filepath}");
        }
        switch (strtolower($this->format)) {
            case 'json':
                $content = Json::encode($data);
                break;
            case 'array':
                $content = "<?php " . PHP_EOL . "return ". $this->exportToArray($data) . ';';
                break;
            default:
                throw new yii\base\NotSupportedException("Not support the fomart '{$this->format}'");

        }
        @flock($fp, LOCK_EX);
        @fwrite($fp, $content);
        @flock($fp, LOCK_UN);
        @fclose($fp);
        return true;
    }

    protected function exportToArray($data, $level = 1, $indent = '    ')
    {
        if (!is_array($data)) {
            return is_string($data) ? "\"$data\"" : $data;
        }
        $lines[] = "array(";
        $count = count($data);
        $i = 1;
        foreach ($data as $key => $value) {
            $suffix = $count != $i ? ',' : '';
            if (is_string($key)) {
                $lines[] = $indent . "\"$key\" => " . $this->exportToArray($value, $level + 1) . $suffix;
            } else {
                $lines[] = $indent . $this->exportToArray($value) . $suffix;
            }
            $i++;
        }
        $lines[] = ')';
        $string = '';
        $prefix = str_repeat($indent, $level - 1);
        $lineCount = count($lines);
        foreach ($lines as $num => $line) {
            if ($num == 0) {
                $string .= $line;
            } else {
                $string .= $prefix . $line;
            }
            $string .= $num < $lineCount - 1 ? PHP_EOL : '';
        }
        return $string;
    }

    protected function getFileExtension()
    {
        switch (strtolower($this->format)) {
            case 'json':
                return '.json';
            case 'array':
                return '.php';
            default:
                return '';
        }
    }
}
