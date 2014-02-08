<?php

namespace OspariAdmin\Model;

Class Setting extends \NZ\ActiveRecord {

    protected $settings = array();
    private $fetched = FALSE;
    static $stdObject;

    public function getTableName() {
        return OSPARI_DB_PREFIX.'settings';
    }
    
    public function add($key, $value) {
        
    }

    public function get($key) {
        $this->fetchAll();
        if (isset($this->settings[$key])) {
            return $this->settings[$key];
        }
        return NULL;
    }

    protected function setToParent($name, $value) {
        parent::set($name, $value);
    }

    protected function getFromParent($name) {
        return parent::get($name);
    }
    protected function saveParent() {
        parent::save();
    }
    
     

    public function set($name, $value) {
        $this->settings[$name] = $value;
    }

    public function save() {
        
        foreach ($this->settings as $key => $value) {
            $rec = new Setting(array('key_name' => $key ));
           
            $rec->setToParent('key_name', $key);
            $rec->setToParent('key_value', $value);
            if( $id = $rec->getFromParent('id') ){
               
                $rec->update( array( 'id' => $id ) );
                 
            }else{
                $rec->saveParent();
            }
            
        }
    }

    public function fetchAll() {
        if ($this->fetched) {
            return TRUE;
        }

        foreach (self::findAll(array()) as $setting) {
            $k = $setting->getFromParent('key_name');
            $v = $setting->getFromParent('key_value');
            $this->settings[$k] = $v;
        }
        $this->fetched = TRUE;
    }

    public function __get($name) {
        return $this->get($name);
    }

    public function __set($name, $value) {
        return $this->set($name, $value);
    }

    public function toHttpRequest(\NZ\HttpRequest $req = NULL) {
        $this->fetchAll();
        
        foreach ($this->settings as $key => $value) {
            $req->set($key, $value);
        }
        return $req;
    }

    public function toStdObject() {
        $this->fetchAll();
        $ret = new \stdClass();
        foreach ($this->settings as $key => $value) {
            $ret->$key = $value;
        }
        return $ret;
    }

    static function getAsStdObject(){
        if( self::$stdObject ){
            return self::$stdObject;
        }
        
        $s = new Setting();
        $s->fetchAll();
        
        $std = $s->toStdObject();
        self::$stdObject = $std;
        
        return $std;
        
    }
    
}
