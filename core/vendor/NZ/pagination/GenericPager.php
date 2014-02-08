<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace NZ;

class GenericPager {

    private $select;
    private $where = array();
    private $order;
    private $perPage;
    private $page;
    private $pager;

    /**
     * ?filter[lang]=de&filiter[state]=
     * @param type $object
     * @param type $req
     * @param type $allowedFilters
     * @param type $allowedSearches
     * @param type $allowedSortFields
     * @param type $perPage
     */
    public function __construct(\NZ\ActiveRecord $object, $req, $allowedFilters = array(), $allowedSortFields = array(), $perPage = 20) {
        
        $this->page = $req->get('page');
        $this->perPage = $perPage;
        $this->object = $object;

        $where = $this->createWhereByRequest($req, $allowedFilters);
        $order = $this->createOrderByRequest($req, $allowedSortFields);
        $this->pager = new \NZ\Pager($object, $where, $this->page, $this->perPage, $order);
    }

    private function createOrderByRequest($req, $allowedOrders) {
        $allowedOrderTypes = array(
            'ASC' => 'ASC',
            'DESC' => 'DESC'
        );

        $order = array();

        $order_type = '';

        if (in_array($req->getQuery('order_type'), $allowedOrderTypes)) {
            $order_type = $req->getQuery('order_type');
        }

        if (in_array($req->getQuery('order_by'), $allowedOrders)) {
            $order[$req->getQuery('order_by')] = $order_type;
        }

        
        return $order;
    }

    private function createWhereByRequest($req, $allowedFilters) {
        $where = $this->where;

        foreach ($req->getQueryParams() as $k => $v) {
            if (in_array($k, $allowedFilters)) {
                $where[$k] = $v;
            }
        }

        $this->where = $where;

        return $where;
    }

    /**
     * 
     * @return \Zend\Db\Sql\Select
     */
    public function getSelect() {
        return $this->pager->getSelect();
    }

    /**
     * 
     * @return \NZ\Pager
     */
    public function create() {
        //echo $this->pager->getSelect()->getSqlString();
        return $this->pager;
    }

}

?>
