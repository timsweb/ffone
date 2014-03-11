<?php

namespace Fone\Model;
/**
 *
 */
class Team
{

    public function getCode()
    {
        return $this->_get('code');
    }

    public function setCode($code)
    {
        $this->_set('code', $code);
        return $this;
    }

    public function getName()
    {
        return $this->_get('name');
    }

    public function setName($name)
    {
        $this->_set('name', $name);
        return $this;
    }

    public function getCost()
    {
        return $this->_get('cost');
    }

    public function setCost($cost)
    {
        $this->_set('cost', $cost);
        return $this;
    }
}