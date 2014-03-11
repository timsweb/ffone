<?php

namespace Fone\Mapper;

abstract class AbstractMapper
{
    protected $_tableName;

    protected $_class;

    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $_db;

    public function __construct(\Doctrine\DBAL\Connection $db, $table, $class)
    {
        $this->setDb($db);
        $this->setTableName($table);
        $this->setClass($class);
    }

    public function getTableName()
    {
        return $this->_tableName;
    }

    public function getClass()
    {
        return $this->_class;
    }

    public function getDb()
    {
        return $this->_db;
    }

    public function setTableName($tableName)
    {
        $this->_tableName = $tableName;
        return $this;
    }

    public function setClass($class)
    {
        $this->_class = $class;
        return $this;
    }

    public function setDb($db)
    {
        $this->_db = $db;
        return $this;
    }


    /**
     * get a single row
     * @param array|scalar $where
     * @return \Fone\Model\AbstractModel
     */
    public function get($where)
    {
        if (!is_array($where)) {
            $where = ['id' => $where];
        }
        $query = 'select * from ' . $this->getTableName() . ' where ';
        $query .= $this->_toWhereString($where);
        $row = $this->getDb()->fetchAssoc($query, array_values($where));
        return $this->_hydrate($row);
    }

    protected function _toWhereString(&$where)
    {
        $whereStringParts = $where;
        array_walk($whereStringParts, function(&$val, $key) {
            if ($val === null) {
                $val = $key . ' is NULL';
            } else {
                $val = $key . ' = ?';
            }
        });
        $whereString = implode(' AND ' , $whereStringParts);
        $where = array_filter($where, function($val){
            return $val !== null;
        });
        return $whereString;
    }

    /**
     * Save a model
     * @param \Fone\Model\AbstractModel $model
     * @return int
     */
    public function save(\Fone\Model\AbstractModel $model)
    {
        $modelData = $model->toDataArray();
        if (isset($modelData['id'])) {
            if ($modelData['id']) {
                return $this->getDb()->update($this->getTableName(), $modelData, ['id' => $modelData['id']]);
            } else {
                $this->getDb()->insert($this->getTableName(), $modelData);
                if (is_callable([$model, 'setId'])) {
                    $lastId = $this->getDb()->lastInsertId();
                    $model->setId($lastId);
                    return $lastId;
                }
            }
        } else {
            $query = 'REPLACE INTO ' . $this->getTableName()
                .' (' . implode(', ', array_keys($modelData)) . ') VALUES (' .
                implode(', ', array_fill(0, count($modelData), '?'))
                . ')';
            return $this->getDb()->executeUpdate($query, array_values($modelData));
        }

    }

    protected function _hydrate($row)
    {
        $class = $this->getClass();
        return new $class($row);
    }

    protected function _hydrateArray(array $rows)
    {
        return array_map([$this, '_hydrate'], $rows);
    }
}