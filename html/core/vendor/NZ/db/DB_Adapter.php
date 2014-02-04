<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of NZ_DB_Factory
 *
 * @author 28h
 */

namespace NZ;
use Zend\Db\Adapter\Adapter;

class DB_Adapter {
    //put your code here
    static private $instance;


    static public function getInstance(){
        if( self::$instance ){
            return self::$instance;
        }
        
        $conf = Config::getInstance();
        $db = $conf->get('db_write');
        $adapter = new Adapter(array(
            'driver' => 'Pdo_Mysql',
            'database' => $db['database'],
            'username' => $db['username'],
            'password' => $db['password'],
            'host' => $db['host'],
             'options' => $db['options'],
         ));
        
        self::$instance = $adapter;
        
        return $adapter;
        
    }
    
    static public function getReadInstance(){
        return self::getInstance();
    }
    
    static public function getWriteInstance(){
        return self::getInstance();
    }
    
}


