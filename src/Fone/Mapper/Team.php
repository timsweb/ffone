<?php

namespace Fone\Mapper;

/**
 *
 */
class Team extends AbstractMapper
{

    public function get($where)
    {
        if (!is_array($where)) {
            $where = ['code' => $where];
        }
        return parent::get($where);
    }

    public function fetchAll()
    {
        $result = $this->getDb()->executeQuery('select * from ' . $this->getTableName() . ' order by cost desc');
        $rows   = $result->fetchAll(\PDO::FETCH_ASSOC);
        return $this->_hydrateArray($rows);
    }

}
