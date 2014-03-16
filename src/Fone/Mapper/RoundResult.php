<?php

namespace Fone\Mapper;
/**
 *
 */
class RoundResult extends AbstractMapper
{
    /**
     *
     * @return int
     */
    public function getLastLoadedRoundId()
    {
        return $this->getDb()->fetchColumn('select max(roundId) from ' . $this->getTableName());
    }
}