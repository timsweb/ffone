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
    public function getNextRound($asOf = null)
    {
        if (!$asOf) {
            $asOf = time();
        }
        $row = $this->getDb()->fetchAssoc('select * from rounds where racedate > ? order by racedate asc limit 1', [$asOf]);
        return $this->_hydrate($row);
    }
}