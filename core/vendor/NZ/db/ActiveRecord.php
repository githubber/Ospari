<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Record
 *
 * @author 28h
 */

namespace NZ;

class ActiveRecord {

    //put your code here

    protected $tableName;
    static protected $staticTableName;
    protected $primaryKey = 'id';
    protected $row = array();

    public function __construct($where = array()) {
        if ($where) {
            $this->fetch($where);
        }
    }

    public function getTableName() {

        if ($this->tableName) {

            return $this->tableName;
        }

        /*
          if (self::$staticTableName) {
          return self::$staticTableName;
          }
         * 
         */

        $className = get_called_class();
        return $this->tableName = $this->findTableName($className);
    }

    private function findTableName($className) {
        $tableName = $className . 's';
        $arr = explode("\\", $tableName);
        $tableName = end($arr);
        $tableName = strtolower($tableName);

        /*
          if ($tableName == 'activerecords') {
          if (self::$staticTableName) {
          $tableName = self::$staticTableName;
          }
          }
         * 
         */
        $this->tableName = $tableName;
        //var_dump($tableName);
        return $tableName;
    }

    private function setTableName($name) {
        $this->tableName = $name;
    }

    protected function getPrimaryKey() {
        return $this->primaryKey;
    }

    /**
     * 
     * @param type $where
     * @return \NZ\ActiveRecord|null
     */
    static public function findLast($where) {
        $className = get_called_class();
        $obj = new $className();
        $obj->fetch($where, $which = 'last');
        if ($obj->row) {
            return $obj;
        }
        return NULL;
    }

    /**
     * 
     * @param type $where
     * @return \NZ\ActiveRecord|null
     */
    static public function findFirst($where) {
        $className = get_called_class();
        $obj = new $className();
        $obj->fetch($where, $which = 'last');
        if ($obj->row) {
            return $obj;
        }
        return NULL;
    }

    /**
     * 
     * @param type $where
     * @return \NZ\ActiveRecord|null
     */
    static public function findOne($where) {
        $className = get_called_class();
        $obj = new $className();
        $obj->fetch($where);
        if ($obj->row) {
            return $obj;
        }
        return NULL;
    }

    /**
     * 
     * @param type $where
     * @return \ArrayObject
     */
    static public function findAll($where) {
        //$obj = new self();
        $className = get_called_class();
        //$tableName = $obj->findTableName($className);
        //$obj->setTableName($tableName);
        $obj = new $className ();
        $ret = array();

        $tableGateway = $obj->getTableGateyway();


        if (is_object($where)) {
            $restSet = $tableGateway->selectWith($where);
        } else {
            $restSet = $tableGateway->select($where);
        }

        $arr = $restSet->toArray();



        foreach ($arr as $row) {
            $obj = new $className ();
            $obj->setRow($row);

            $ret[] = $obj;
        }

        return new \ArrayObject($ret);
    }

    public function __set($name, $value) {
        $this->set($name, $value);
    }

    public function set($name, $value) {
        $this->row[$name] = $value;
    }

    public function setNZ_Request(\NZ\HttpRequest $req) {

        $metadata = new \Zend\Db\Metadata\Metadata($this->getAdapter());
        $colmns = $metadata->getColumnNames($this->getTableName());
        foreach ($colmns as $name) {
            if ($req->isParam($name)) {
                $this->set($name, $req->get($name));
            }
        }
    }

    /**
     * 
     * @return array()
     */
    public function ColumnNames() {
        $metadata = new \Zend\Db\Metadata\Metadata($this->getAdapter());
        $colmns = $metadata->getColumnNames($this->getTableName());
        return $colmns;
    }

    public function get($name) {
        $row = $this->row;
        if (isset($row[$name])) {
            return $row[$name];
        }
    }

    public function __get($name) {
        return $this->get($name);
    }

    protected function setRow($row) {
        $this->row = $row;
    }

    public function save() {
        $set = $this->row;
        $tableGateway = $this->getTableGateyway();

        if ($id = $this->get($this->getPrimaryKey())) {
            $where = array($this->getPrimaryKey() => $id);
            $tableGateway->update($set, $where);
        } else {
            $tableGateway->insert($set);
            $id = $tableGateway->getLastInsertValue();
            if ($id) {
                $where = array($this->getPrimaryKey() => $id);
            } else {
                $where = $set;
            }
        }

        $this->fetch($where);
    }

    public function delete($where) {
        if (!$where) {
            //throw new \Exception('No delete with empty where');
            $where = $this->getDefaultWhere();
        }

        $tableGateway = $this->getTableGateyway();
        $tableGateway->delete($where);
    }

    public function update($where = NULL) {
        if (!$where) {
            $where = $this->getDefaultWhere();
        }

        $tableGateway = $this->getTableGateyway();


        $tableGateway->update($this->row, $where);
        $this->fetch($where);
    }

    private function getAdapter() {
        return \NZ\DB_Adapter::getInstance();
    }

    /**
     * 
     * @return \Zend\Db\TableGateway\TableGateway
     */
    public function getTableGateyway() {
        $tableName = $this->getTableName();

        return new \Zend\Db\TableGateway\TableGateway($tableName, $this->getAdapter());
    }

    private function getDefaultWhere() {
        return array($this->getPrimaryKey() => $this->get($this->getPrimaryKey()));
    }

    /**
     * @todo Add Lomit 0, 1
     * @param type $where
     * @return type
     */
    private function fetch($where, $which = NULL) {
        if (is_numeric($where)) {
            $where = array($this->getPrimaryKey() => $where);
        }

        if (!is_object($where)) {
            $select = new \Zend\Db\Sql\Select($this->getTableName());
            $select->where($where);
            if ($which == 'last') {
                $select->order($this->getPrimaryKey() . ' DESC');
            }

            if ($which == 'first') {
                $select->order($this->getPrimaryKey() . ' ASC');
            }
            $select->offset(0);
            $select->limit(1);

            $tableGateway = $this->getTableGateyway();
            $restSet = $tableGateway->selectWith($select);

            //echo json_encode( $select->getSqlString() );
        } else {

            $tableGateway = $this->getTableGateyway();
            $restSet = $tableGateway->select($where);
        }



        $current = $restSet->current();
        if (!$current) {
            return;
        }

        //$current->getArrayCopy();



        $this->setRow($current->getArrayCopy());
    }

    /**
     * 
     * @param \DateTime $dateTime
     * @return \NZ\ActiveRecord
     */
    public function setCreatedAt(\DateTime $dateTime = NULL) {
        if ($dateTime == NULL) {
            $dateTime = new \DateTime();
        }
        $this->created_at = $dateTime->format('Y-m-d H:i:s');
        return $this;
    }

    public function setUpdatedAt(\DateTime $dateTime) {
        $this->updated_at = $dateTime->format('Y-m-d H:i:s');
        return $this;
    }

    public function setEditedAt(\DateTime $dateTime) {
        $this->edited_at = $dateTime->format('Y-m-d H:i:s');
        return $this;
    }

    public function setDeletedAt(\DateTime $dateTime) {
        $this->deleted_at = $dateTime->format('Y-m-d H:i:s');
        return $this;
    }

    public function setDateTime($name, \DateTime $dateTime) {
        $this->set($name, $dateTime->format('Y-m-d H:i:s'));
        return $this;
    }

    public function getAsDateTime($property) {
        return new \DateTime($this->get($property));
    }

    /**
     * 
     * @param type $tableName
     * @param type $where
     * @return \NZ\ActiveRecord
     */
    static public function FetchObject($tableName, $where = array()) {
        $ob = new ActiveRecord();
        //self::$staticTableName = $tableName;
        $ob->setTableName($tableName);
        if ($where) {
            $ob->fetch($where);
        }

        return $ob;
    }

    public function toArray() {
        return $this->row;
    }

    public function toHttpRequest(\NZ\HttpRequest $req = NULL) {
        if (!$req) {
            $req = new \NZ\HttpRequest();
        }

        foreach ($this->row as $k => $v) {
            $req->set($k, $v);
        }

        return $req;
    }

    public function getFloat($name) {
        $row = $this->row;
        if (isset($row[$name])) {
            return floatval($row[$name]);
        }
        return 0;
    }

    public function getInt($name) {
        $row = $this->row;
        if (isset($row[$name])) {
            return intval($row[$name]);
        }
        return 0;
    }

    public function query($sql) {

        $db = DB_Adapter::getInstance();
        return $db->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
    }

    public function count($where = array()) {
        $select = new \Zend\Db\Sql\Select( );
        $select->where($where);
        $select->from($this->getTableName());
        $select->columns(array('_total' => new \Zend\Db\Sql\Predicate\Expression('count(*)')));

        $tg = $this->getTableGateyway();
        $resultSet = $tg->selectWith($select);
        $item = $resultSet->current();
        return $item['_total'];
    }

    public function sum($field, $where) {
        $select = new \Zend\Db\Sql\Select( );
        $select->where($where);
        $select->from($this->getTableName());
        $select->columns(array('_sum' => new \Zend\Db\Sql\Predicate\Expression('sum(' . $field . ')')));


        $tg = $this->getTableGateyway();
        $resultSet = $tg->selectWith($select);
        $item = $resultSet->current();
        if (isset($item['_sum'])) {
            return $item['_sum'];
        }
        return null;
    }

    public function toStdObject() {
        $arg_list = func_get_args();
        if (!$arg_list) {
            $arg_list = array_keys($this->row);
        }

        $obj = new \stdClass();
        foreach ($arg_list as $arg) {

            $obj->$arg = $this->get($arg);
        }
        return $obj;
    }

    public function generateSelect($nzmap, $where, $order) {
        $select = new \Zend\Db\Sql\Select( );
        $select->where($where);

        $select->from($this->getTableName());
        $select->order($order);
        return $select;
    }

    public function getObjectKey($p = 'id') {
        return $this->getObjectName() . '.' . $this->getObjectId($p);
    }

    public function getObjectId($p = 'id') {
        return $this->get($p);
    }

    public function getObjectName() {
        return $this->getTableName();
    }

    public function increase($field, $by) {
        $sql = "UPDATE " . $this->getTableName() . " set {$field} = {$field}+{$by} WHERE id={$this->id} ";
        $this->query($sql);
    }

    public function decrease($field, $by) {
        $sql = "UPDATE " . $this->getTableName() . " set {$field} = {$field}-{$by} WHERE id={$this->id} ";
        $this->query($sql);
    }

    /**
     * 
     * @param type $objList
     * @param type $foreignKey
     * @param type $key
     * @return array
     */
    public function fetchByObjectList($objList, $foreignKey, $key = NULL, $whereString = '') {
        $ids = array();
        foreach ($objList as $obj) {
            $id = $obj->get($foreignKey);
            $ids[$id] = $id;
        }

        if (!$ids) {
            return array();
        }

        $className = get_called_class();
        $thisObject = new $className();

        if (!$key) {
            $key = $thisObject->getPrimaryKey();
        }

        $cl = " {$key} IN ('" . implode("', '", $ids) . "') " . $whereString;
        if (isset($_GET['dd'])) {
            echo $cl;
        }
        //$cl = "WHERE {$key} IN ('".implode(',', $ids)."') ";
        $ret = array();
        foreach (call_user_func_array(array($className, 'findAll'), array($cl)) as $object) {
            $id = $object->get($key);
            if ($id) {
                $ret[$id] = $object;
            }
        }

        return $ret;
    }

}
