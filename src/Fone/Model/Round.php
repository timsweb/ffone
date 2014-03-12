<?php

namespace Fone\Model;

class Round extends AbstractModel
{

    public function getId()
    {
        return $this->_get('id');
    }

    public function setId($id)
    {
        $this->_set('id', $id);
        return $this;
    }

    public function getCountryCode()
    {
        return $this->_get('countryCode');
    }

    public function setCountryCode($countryCode)
    {
        $this->_set('countryCode', $countryCode);
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

    public function getRacedate()
    {
        return $this->_get('racedate');
    }

    public function setRacedate($racedate)
    {
        $this->_set('racedate', $racedate);
        return $this;
    }

    public function getLocation()
    {
        return $this->_get('location');
    }

    public function setLocation($location)
    {
        $this->_set('location', $location);
        return $this;
    }

}
