<?php

namespace Fone\Mapper;

class UserTeam extends AbstractMapper
{
    public function getTeamForRound($userId, $roundId)
    {
        $row = $this->getDb()->fetchAssoc('select * from '
            . $this->getTableName()
            . ' where userId = ? and effectiveFrom <= ? order by effectiveFrom asc limit 1', [$userId, $roundId]);
        return $this->_hydrate($row);
    }

    public function getCurrentTeam($userId)
    {
        $row = $this->getDb()->fetchAssoc('select * from '
            . $this->getTableName()
            . ' where userId = ? order by effectiveFrom desc limit 1', [$userId]);
        return $this->_hydrate($row);
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