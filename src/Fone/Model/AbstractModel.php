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

    public function addReference($name, callable $callback)
    {
        $this->_references[$name] = $callback;
        return $this;
    }

    public function getReference($name)
    {
        if (!isset($this->_references[$name])) {
            return null;
        }
        //replace reference with returned value to act as a simple in memory cache
        if (is_callable($this->_references[$name])) {
            $this->_references[$name] = call_user_func($this->_references[$name], $this);
        }
        return $this->_references[$name];
    }

    public function __construct(array $modelData = [])
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