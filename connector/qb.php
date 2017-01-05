<?php
/**
 *  __  __     ______     ______     __     ______
 * /\ \/ /    /\  ___\   /\  __ \   /\ \   /\  == \
 * \ \  _"-.  \ \  __\   \ \ \/\ \  \ \ \  \ \  __<
 *  \ \_\ \_\  \ \_____\  \ \_____\  \ \_\  \ \_\ \_\
 *   \/_/\/_/   \/_____/   \/_____/   \/_/   \/_/ /_/
 *
 * Copyright (c) 2017. Developed by Zackary Pedersen, all rights reserved.
 * zackary@snaju.com - keoir.com - @keoir
 */

/**
 * Class QueryBuilder
 */
class QueryBuilder
{

    /**
     * @param $tableName
     * @param string $sqlPart
     * @param array $dataPart
     * @param array $selectParts
     * @return QueryObject
     */
    static function init($tableName, $sqlPart = "", $dataPart = [], $selectParts = [])
    {
        return new QueryObject($tableName, $sqlPart, $dataPart, $selectParts);
    }

    /**
     * @param QueryObject $obj
     * @param bool $debug
     * @return Query
     */
    static function select(\QueryObject $obj, $debug = false)
    {
        $table = $obj->getTable();
        $q = "SELECT ";
        $q .= $obj->getSelectStmt();
        $q .= " FROM $table";
        if ($obj->getSqlPart() != "") {
            $part = $obj->getSqlPart();
            $q .= " $part";
        }

        return self::q(new QueryStatment($q, $obj->getPartData()), $debug);
    }

    /**
     * @param QueryStatment $stm
     * @param bool $debug
     * @return Query
     */
    private static function q(\QueryStatment $stm, $debug = false)
    {
        return new Query($stm->getSql(), $stm->getData(), "default", $debug);
    }

    /**
     * @param QueryObject $obj
     * @param bool $debug
     * @return Query
     */
    static function insert(\QueryObject $obj, $debug = false)
    {
        $table = $obj->getTable();
        $q = "INSERT INTO $table";


        $keys = "";
        $values = "";
        $data = [];

        foreach ($obj->getProperties() as $key => $value) {
            $keys .= "$key,";
            $values .= "?,";
            $data[] = $value;
        }

        $keys = rtrim($keys, ",");
        $values = rtrim($values, ",");

        $q .= " ($keys) VALUES ($values)";

        return self::q(new QueryStatment($q, $data), $debug);
    }

    /**
     * @param QueryObject $obj
     * @param bool $debug
     * @return Query
     */
    static function update(\QueryObject $obj, $debug = false)
    {
        $table = $obj->getTable();
        $q = "UPDATE $table SET ";
        $data = [];
        foreach ($obj->getProperties() as $key => $value) {
            $q .= "$key = ?,";
            $data[] = $value;
        }
        $q = rtrim($q, ",");
        $part = $obj->getSqlPart();
        $q .= " $part";

        foreach ($obj->getPartData() as $value) {
            $data[] = $value;
        }

        return self::q(new QueryStatment($q, $data), $debug);
    }

    /**
     * @param QueryObject $obj
     * @param bool $debug
     * @return Query
     */
    static function delete(\QueryObject $obj, $debug = false)
    {
        $table = $obj->getTable();
        $q = "DELETE FROM $table";
        $part = $obj->getSqlPart();
        $q .= " $part";

        return self::q(new QueryStatment($q, $obj->getPartData()), $debug);
    }
}

/**
 * Class QueryStatment
 */
class QueryStatment
{
    /**
     * @var
     */
    private $sql;
    /**
     * @var
     */
    private $data;

    /**
     * QStatment constructor.
     * @param $sql
     * @param $data
     */
    public function __construct($sql, $data)
    {
        $this->sql = $sql;
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function getSql()
    {
        return $this->sql;
    }

    /**
     * @param mixed $sql
     */
    public function setSql($sql)
    {
        $this->sql = $sql;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }
}

/**
 * Class QueryObject
 */
class QueryObject
{
    /**
     * @var
     */
    private $table;
    /**
     * @var string
     */
    private $sqlPart;
    /**
     * @var
     */
    private $sql;
    /**
     * @var array
     */
    private $partData;
    /**
     * @var stdClass
     */
    private $properties;
    /**
     * @var array
     */
    private $selectParts;
    /**
     * @var string
     */
    private $selectStmt;

    /**
     * QObj constructor.
     * @param $table
     * @param string $sqlPart
     * @param array $partData
     */
    public function __construct($table, $sqlPart = "", $partData = [], $selectParts = [])
    {
        $this->properties = new stdClass;
        $this->table = $table;
        $this->sqlPart = $sqlPart;
        $this->partData = $partData;
        $this->selectParts = $selectParts;

        if (count($selectParts) == 0) {
            $this->selectStmt = "*";
        } else {
            foreach ($selectParts as $selectPart) {
                $this->selectStmt .= $selectPart . ",";
            }
            $this->selectStmt = rtrim($this->selectStmt, ",");
        }

    }

    /**
     * @param $name
     * @return null
     */
    function __get($name)
    {
        if (isset($this->properties->{$name})) {
            return $this->properties->{$name};
        } else {
            return null;
        }
    }

    /**
     * @param $name
     * @param $value
     */
    function __set($name, $value)
    {
        $this->properties->{$name} = $value;
    }

    /**
     * @return string
     */
    public function getSelectStmt()
    {
        return $this->selectStmt;
    }

    /**
     * @param string $selectStmt
     */
    public function setSelectStmt($selectStmt)
    {
        $this->selectStmt = $selectStmt;
    }

    /**
     * @return mixed
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * @param mixed $properties
     */
    public function setProperties($properties)
    {
        $this->properties = $properties;
    }

    /**
     * @return mixed
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @param mixed $table
     */
    public function setTable($table)
    {
        $this->table = $table;
    }

    /**
     * @return mixed
     */
    public function getSqlPart()
    {
        return $this->sqlPart;
    }

    /**
     * @param mixed $sqlPart
     */
    public function setSqlPart($sqlPart)
    {
        $this->sqlPart = $sqlPart;
    }

    /**
     * @return mixed
     */
    public function getSql()
    {
        return $this->sql;
    }

    /**
     * @param mixed $sql
     */
    public function setSql($sql)
    {
        $this->sql = $sql;
    }

    /**
     * @return mixed
     */
    public function getPartData()
    {
        return $this->partData;
    }

    /**
     * @param mixed $partData
     */
    public function setPartData($partData)
    {
        $this->partData = $partData;
    }

    /**
     * @param bool $debug
     * @return Query
     */
    public function select($debug = false)
    {
        return QueryBuilder::select($this, $debug);
    }

    /**
     * @param bool $debug
     * @return Query
     */
    public function update($debug = false)
    {
        return QueryBuilder::update($this, $debug);
    }

    /**
     * @param bool $debug
     * @return Query
     */
    public function delete($debug = false)
    {
        return QueryBuilder::delete($this, $debug);
    }

    /**
     * @param bool $debug
     * @return Query
     */
    public function insert($debug = false)
    {
        return QueryBuilder::insert($this, $debug);
    }
}

?>