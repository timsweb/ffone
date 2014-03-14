<?php

namespace Fone\Model;
/**
 *
 */
class RoundResult extends AbstractModel
{
    static final public function getRaceScore($position)
    {
        $scores = [50, 45, 40, 35, 30, 27, 24, 22, 20, 18, 16, 14, 13, 12, 11, 10, 9, 8, 7, 6, 5, 4];
        return $scores[$position - 1];
    }

    static final public function getQualiScore($position)
    {
        $scores = array_reverse(range(4, 25));
        return $scores[$position - 1];
    }


    public function getRoundId()
    {
        return $this->_get('roundId');
    }

    public function setRoundId($roundId)
    {
        $this->_set('roundId', $roundId);
        return $this;
    }

    public function getDriverCode()
    {
        return $this->_get('driverCode');
    }

    public function setDriverCode($driverCode)
    {
        $this->_set('driverCode', $driverCode);
        return $this;
    }

    public function getRacePosition()
    {
        return $this->_get('racePosition');
    }

    public function setRacePosition($racePosition)
    {
        $this->_set('racePosition', $racePosition);
        return $this;
    }

    public function getQualifyingPosition()
    {
        return $this->_get('qualifyingPosition');
    }

    public function setQualifyingPosition($qualifyingPosition)
    {
        $this->_set('qualifyingPosition', $qualifyingPosition);
        return $this;
    }

    public function getFastestLap()
    {
        return $this->_get('fastestLap');
    }

    public function setFastestLap($fastestLap)
    {
        $this->_set('fastestLap', $fastestLap);
        return $this;
    }

    public function getScore(&$breakdown)
    {
        if (!is_array($breakdown)) {
            $breakdown = [];
        }
        $breakdown['race'] = self::getRaceScore($this->getRacePosition());
        $breakdown['quali'] = self::getQualiScore($this->getQualifyingPosition());
        $breakdown['fastestLap'] = ($this->getFastestLap())? 10 : 0;
        $breakdown['positionsGained'] = ($this->getQualifyingPosition() - $this->getRacePosition() > 0)? $this->getQualifyingPosition() - $this->getRacePosition() : 0;
        $breakdown['bonus'] = ($this->getQualifyingPosition() === 1 && $this->getRacePosition() === 1)? 15 : 0;
        $total = array_sum($breakdown);
        return $total;
    }
}