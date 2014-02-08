<?php

/**
 * 
 */
namespace NZ;

Class ClassLoader{
    
    protected $map = array();
    static protected $instance;

    private function __construct() {
        
    }

    static public function getInstance(){
        if( self::$instance == NULL ){
            self::$instance = new ClassLoader();
        }
        return self::$instance;
    }

        public function register() {
        spl_autoload_register( array( $this, 'load' ), TRUE, TRUE );
        
    }
    
    public function registerAutoloadMap( $path ){
        if(is_array($path) ){
            $map = $path;
        }else{
            $map = include ($path);
        }
        if( !is_array($map) ){
            throw new \Exception('Invalid class map');
        }
        $this->map = array_merge($this->map, $map);;
    }
    
    public function load( $className ){
        $map = $this->map;
        if( isset( $this->map[$className] ) ){
            include ($this->map[$className]);
            return;
        }
        
    }
    
    
}
