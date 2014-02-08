<?php

namespace NZ;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Expression;

trait DB_Trait {

    protected $rowGateway;
    private $data = array();

    protected function subConsruct($table, $arr = array()) {
        parent::__construct($table, DB_Adapter::getInstance());

        if ($arr) {
            //echo '......';
            $rowset = $this->select($arr);
            $this->rowGateway = $rowset->current();
        }
    }

    public function count($where = array()) {


        $select = new \Zend\Db\Sql\Select( );
        $select->where($where);
        $select->from($this->getTable());
        $select->columns( array('_total' => new Expression('count(*)') ) );


      
        $resultSet = $this->selectWith($select);
        $item = $resultSet->current();
        return $item['_total'];
    }

    public function set($property, $value) {
        $this->data[$property] = $value;
    }

    public function __set($property, $value) {
        $this->data[$property] = $value;
    }

    public function __get($fieldName) {

        if ($this->rowGateway) {
            return $this->rowGateway->$fieldName;
        }
    }

    public function get($fieldName) {
        if ($this->rowGateway) {
            return $this->rowGateway->$fieldName;
        }
    }

    public function date($timestamp = null) {
        if ($timestamp == null) {
            $timestamp = time();
        }
        return date('Y-m-d', $timestamp);
    }

    public function datetime($timestamp = null) {
        if ($timestamp == null) {
            $timestamp = time();
        }
        return date('Y-m-d H:i:s', $timestamp);
    }

    public function time($timestamp = null) {
        if ($timestamp == null) {
            $timestamp = time();
        }
        return date('H:i:s', $timestamp);
    }

    public function now() {
        return date('Y-m-d H:i:s');
    }

}