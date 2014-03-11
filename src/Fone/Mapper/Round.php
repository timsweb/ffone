<?php

namespace Fone\Mapper;
/**
 *
 */
class Round extends AbstractMapper
{
    /**
     * @return \Fone\Model\Round
     */
    public function getNextRound()
    {
        $row = $this->getDb()->fetchAssoc('select * from rounds where racedate > ? order by racedate asc limit 1', [time()]);
        return $this->_hydrate($row);
    }
}