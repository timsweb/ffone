<?php

namespace Fone\Mapper;
use Fone\Model\Driver as DriverModel;

class Driver extends AbstractMapper
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

    protected function _hydrate($row)
    {
        $model = parent::_hydrate($row); /*@var $model DriverModel*/
        if ($model) {
            $model->addReference('team', function($m) {
                return $this->getApp()['teamMapper']->get($m->getTeam());
            });
        }

        return $model;
    }
}
