<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Pager
 *
 * @author 28h
 */

namespace NZ;

class Pager {

    /**
     *
     * @var Zend\Db\TableGateway\TableGateway|\NZ\ActiveRecord 
     */
    protected $table;
    protected $page;
    protected $offset;
    protected $where;
    protected $perPage;
    protected $order;
    protected $count;
    private $select;
    
    /**
     * 
     * @param type $table
     * @param type $where
     * @param int $page
     * @param type $perPage
     * @param type $order
     */
    public function __construct($table, $where = array(), $page, $perPage, $order = array()) {
        $this->table = $table;
        if (!$page) {
            $page = 1;
        }
        $this->page = $page;

        $this->perPage = $perPage;
        $this->order = $order;
        $this->where = $where;
        $this->limit = $perPage;
        $this->offset = $this->perPage * ( $page - 1 );

        $this->buildSelect();
    }

    /**
     * 
     * @return \Zend\Db\Sql\Select
     */
    public function getSelect() {
        return $this->select;
    }

    public function count() {
        if ($c = $this->count) {
            return $c;
        }

        $object = $this->table;
        if ($object instanceof \NZ\ActiveRecord) {
            $c = $object->count($this->select->where);
            $this->count = $c;
            return $c;
        }

        $c = $this->table->select($this->select->where)->count(); // the count() is a method of the Rowset class and not of the tablegateway class. rowset return after a select is executed by the tablegateway instance
        $this->count = $c;
        return $c;
    }

    private function buildSelect() {

        $table = $this->table;

        if ($table instanceof \NZ\ActiveRecord) {
            $select = new \Zend\Db\Sql\Select($table->getTableName());
        } else {
            $select = new \Zend\Db\Sql\Select($table->getTable());
        }

        $select->where($this->where);
        $select->offset(intval($this->offset));
        $select->limit(intval($this->limit));
        $select->order($this->order);
        $this->select = $select;
    }

    public function getItems() {
        $table = $this->table;
        $select = $this->select;
        $table = $this->table;
        if ($table instanceof \NZ\ActiveRecord) {
            //return call_user_func( array( get_class( $table ), '' ) );
            return call_user_func_array(array(get_class($table), 'findAll'), array($select));
        }

        $resultSet = $table->selectWith($select);
        return $resultSet;
    }

    /**
     * 
     * @return \NZ\Pagination
     */
    public function toPagination() {
        $c = $this->count();
        if (!$c) {
            $total = 0;
        } else {
            $total = ceil( $c / $this->perPage);
        }
        $pg = new Pagination($this->page, $total);

        return $pg;
    }

    public function toPagnination() {
        trigger_error(__METHOD__ . ' depricated.', E_USER_DEPRECATED);
    }

    public function getPage() {
        return $this->page;
    }

    public function getPaginationURL($req) {
        $nzUri = new \NZ\Uri($req->getCurrentUrl());

        $nzUri->removeParam('page');

        if ($nzUri->hasParams()) {
            return $nzUri->__toString() . '&page=';
        }

        return '?page=';
    }

}

