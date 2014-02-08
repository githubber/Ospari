<?php

namespace NZ;

Class Config {

    private $data = array();
    static private $instance;

    private function __construct($data) {
        $this->data = $data;
    }

    static public function setArray($data) {
        self::$instance = new Config($data);
        return self::$instance;
    }
    
    static public function setArrayFile($filePath) {
       $data = include $filePath;
       return self::setArray($data);
    }

    /**
     * 
     * @return \NZ\Config
     * @throws \Exception
     */
    static public function getInstance() {
        if (self::$instance == null) {
            throw new \Exception('No data set');
        }
        return self::$instance;
    }

    static public function setIni($data) {
        
    }

    static public function setXML($data) {
        
    }
    
    public function get( $p ){
        if (isset($this->data[$p])) {
            return $this->data[$p];
        }
        return null;
    }

    public function __get($p) {
        if (isset($this->data[$p])) {
            return $this->data[$p];
        }
        return null;
    }
    
     public function set($k, $v) {
        
        $this->data[$k] = $v;
        return $this;
        
        
    }

}