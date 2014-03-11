<?php

namespace Fone\Mapper;

/**
 *
 */
class Team extends AbstractMapper
{

    public function fetchAll()
    {
        $result = $this->getDb()->executeQuery('select * from ' . $this->getTableName() . ' order by cost desc');
        $rows   = $result->fetchAll(\PDO::FETCH_ASSOC);
        return $this->_hydrateArray($rows);
    }

}
