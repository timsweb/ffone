<?php

namespace Fone\Model;

/**
 *
 */
class UserTeam extends AbstractModel
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

    public function getDriverA()
    {
        return $this->_get('driverA');
    }

    public function setDriverA($driverA)
    {
        $this->_set('driverA', $driverA);
        return $this;
    }

    public function getDriverB()
    {
        return $this->_get('driverB');
    }

    public function setDriverB($driverB)
    {
        $this->_set('driverB', $driverB);
        return $this;
    }

    public function getTeamA()
    {
        return $this->_get('teamA');
    }

    public function setTeamA($teamA)
    {
        $this->_set('teamA', $teamA);
        return $this;
    }

    public function getTeamB()
    {
        return $this->_get('teamB');
    }

    public function setTeamB($teamB)
    {
        $this->_set('teamB', $teamB);
        return $this;
    }

    public function getEffectiveFrom()
    {
        return $this->_get('effectiveFrom');
    }

    public function setEffectiveFrom($effectiveFrom)
    {
        $this->_set('effectiveFrom', $effectiveFrom);
        return $this;
    }

    /**
     * @return \Fone\Model\Driver
     */
    public function getDriverAModel()
    {
        return $this->getReference('driverA');
    }

    /**
     *
     * @return \Fone\Model\Driver
     */
    public function getDriverBModel()
    {
        return $this->getReference('driverB');
    }

    /**
     *
     * @return \Fone\Model\Team
     */
    public function getTeamAModel()
    {
        return $this->getReference('teamA');
    }

    /**
     * @return \Fone\Model\Team
     */
    public function getTeamBModel()
    {
        return $this->getReference('teamB');
    }

    public function getScoreForRound($roundId)
    {
        $total = $this->getDriverAModel()->getScoreForRound($roundId);
        $total += $this->getDriverBModel()->getScoreForRound($roundId);
        $total += $this->getTeamAModel()->getScoreForRound($roundId);
        $total += $this->getTeamBModel()->getScoreForRound($roundId);
        return $total;
    }
}