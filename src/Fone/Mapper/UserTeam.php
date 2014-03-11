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

}