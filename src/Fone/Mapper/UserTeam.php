<?php

namespace Fone\Mapper;

class UserTeam extends AbstractMapper
{
    /**
     *
     * @param int $userId
     * @param int $roundId
     * @return \Fone\Model\UserTeam
     */
    public function getTeamForRound($userId, $roundId)
    {
        $row = $this->getDb()->fetchAssoc('select * from '
            . $this->getTableName()
            . ' where userId = ? and effectiveFrom <= ? order by effectiveFrom desc limit 1', [$userId, $roundId]);
        return $this->_hydrate($row);
    }

    public function getCurrentTeam($userId)
    {
        $row = $this->getDb()->fetchAssoc('select * from '
            . $this->getTableName()
            . ' where userId = ? order by effectiveFrom desc limit 1', [$userId]);
        return $this->_hydrate($row);
    }

    public function getScoreHistory($userId, $roundId)
    {
        $rounds = $this->getApp()['roundMapper']->find('id <= ? order by id asc', [$roundId]);
        $scores = [];
        foreach ($rounds as $round) {
            $team = $this->getTeamForRound($userId, $round->getId());
            $driverABreakdown = [];
            $driverBBreakdown = [];
            $teamADriverABreakdown = [];
            $teamADriverBBreakdown = [];
            $teamBDriverABreakdown = [];
            $teamBDriverBBreakdown = [];
            $team->getDriverAModel()->getScoreForRound($round->getId(), $driverABreakdown);
            $team->getDriverBModel()->getScoreForRound($round->getId(), $driverBBreakdown);
            list ($teamADriverA, $teamADriverB) = $team->getTeamAModel()->getDrivers();
            list ($teamBDriverA, $teamBDriverB) = $team->getTeamBModel()->getDrivers();
            $teamADriverA->getScoreForRound($round->getId(), $teamADriverABreakdown);
            $teamADriverB->getScoreForRound($round->getId(), $teamADriverBBreakdown);
            $teamBDriverA->getScoreForRound($round->getId(), $teamBDriverABreakdown);
            $teamBDriverB->getScoreForRound($round->getId(), $teamBDriverBBreakdown);
            $scores[] = [
                'round' => $round,
                'team' => $team,
                'driverA' => $driverABreakdown,
                'driverB' => $driverBBreakdown,
                'teamADriverA' => $teamADriverABreakdown,
                'teamADriverB' => $teamADriverBBreakdown,
                'teamBDriverA' => $teamBDriverABreakdown,
                'teamBDriverB' => $teamBDriverBBreakdown,
            ];
        }
        return $scores;
    }

    protected function _hydrate($row)
    {
        $model = parent::_hydrate($row);
        if ($model) {
            $model->addReference('teamA', function($m) {
                return $this->getApp()['teamMapper']->get($m->getTeamA());
            });
            $model->addReference('teamB', function($m) {
                return $this->getApp()['teamMapper']->get($m->getTeamB());
            });
            $model->addReference('driverA', function($m) {
                return $this->getApp()['driverMapper']->get($m->getDriverA());
            });
            $model->addReference('driverB', function($m) {
                return $this->getApp()['driverMapper']->get($m->getDriverB());
            });
        }
        return $model;
    }

}