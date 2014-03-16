<?php

namespace Fone\Model;

class UserScoreCache extends AbstractModel
{

    public function getUserId()
    {
        return $this->_get('userId');
    }

    public function setUserId($userId)
    {
        $this->_set('userId', $userId);
        return $this;
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

    public function getTotalScore()
    {
        return $this->_get('totalScore');
    }

    public function setTotalScore($totalScore)
    {
        $this->_set('totalScore', $totalScore);
        return $this;
    }

    public function getRoundScore()
    {
        return $this->_get('roundScore');
    }

    public function setRoundScore($roundScore)
    {
        $this->_set('roundScore', $roundScore);
        return $this;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->getReference('user');
    }

    public function setUser($uesrModel)
    {
        $this->_references['user'] = $uesrModel;
    }
}