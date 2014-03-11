<?php

namespace Fone\Model;

abstract class AbstractModel
{
    protected $_data = [];

    protected $_references = [];

    protected function _get($value)
    {
        return $this->_data[$value];
    }

    protected function _set($key, $value)
    {
        $this->_data[$key] = $value;
    }

    public function __construct(array $modelData)
    {
        foreach ($modelData as $key => $val) {
            $setFunction = 'set' . ucfirst($key);
            if (is_callable([$this, $setFunction])) {
                $this->$setFunction($val);
            }
        }
    }

    public function toDataArray()
    {
        return $this->_data;
    }
}