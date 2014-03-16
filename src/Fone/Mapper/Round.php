<?php

namespace Fone\Mapper;
use Fone\Model\Round as RoundModel;
/**
 *
 */
class Round extends AbstractMapper
{
    /**
     * @return RoundModel
     */
    public function getNextRound($asOf = null)
    {
        if (!$asOf) {
            $asOf = time();
        }
        $row = $this->getDb()->fetchAssoc('select * from rounds where racedate > ? order by racedate asc limit 1', [$asOf]);
        return $this->_hydrate($row);
    }

    /**
     *
     * @return RoundModel
     */
    public function getLastRound()
    {
        $row = $this->getDb()->fetchAssoc('select * from rounds where racedate < ? order by racedate desc limit 1', [time()]);
        return $this->_hydrate($row);
    }
}