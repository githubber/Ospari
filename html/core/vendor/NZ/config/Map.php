<?php


namespace NZ;

Class Map{
	private $params = array();


	function __construct( $params = array() ){
		$this->params = $params;
	}


	
	
	public function isParam( $p ){
		return isset( $this->params[$p] );
	}

	public function toArray( ){
		return $this->params;
	}

	public function __get($p){
		if( isset($this->params[$p]) ){
			return $this->params[$p];
		}

		return null;
	}
	
	public function __set( $k, $v ){
		
		$this->params[$k] = $v;
		

		return null;
	}	

	public function getTime( $p ){
		if( !isset( $this->params[$p] ) ){
			return 0;
		}

		return strtotime( $this->params[$p] );
	}


	public function match( $k, $arr ){
		if( !isset( $this->params[$k] )){
			return null;
		}
		$v = trim( $this->params[$k] );
		$keys = array_flip( $arr );

		if( isset( $keys[$v] ) ){
			return $v;
		}
		return null;
	}

	public function get($k){
		if(isset($this->params[$k])){
			return trim( $this->params[$k] );
		}
		return null;
	}

	public function getLine( $k ){
		if(isset($this->params[$k])){
			$lines =  explode("\n", $this->params[$k]);
			if( isset( $lines[1] ) ){
				return null;
			}
			return $this->params[$k];
		}
		return null;
	}


	public function set($k, $v){
		$this->params[$k] = $v;
	}


	public function getArray($k){
		if( !isset($this->params[$k]) ){
			return array();
		}
		return (array) $this->params[$k];

	}

	function getInt($k){
		if(isset($this->params[$k])){
			return intval($this->params[$k]);
		}else{
			return 0;
		}
	}

	function getNumeric( $k ){
		if( isset( $this->params[$k] )){
			if( is_numeric( $this->params[$k] ) ) {
				return $this->params[$k];
			}
		}

		return 0;

	}


	function getString($k){
		return strval( $this->params[$k] );
	}
	function getAlNum($k){
		if( !isset($this->params[$k]) ){
			return null;
		}


		if(ctype_alnum(trim($this->params[$k]))){
			return $this->params[$k];
		}else{
			return null;
		}
	}
	
	
	function getEmail($k){
		if( !isset($this->params[$k]) ){
			return null;
		}

		$email = strtolower($this->params[$k]);
		if(!preg_match("/^([_[:alnum:]-]+)(\.[_[:alnum:]-]+)*@([[:alnum:]\.-]+)([[:alnum:]])\.([[:alpha:]]{2,4})$/",$email)){
			return null;
		}else{
			return $email;
		}
	}

	function getFloat($k){
		if(!isset($this->params[$k])){
			return floatval(0);
		}
		return floatval($this->params[$k]);
	}

	function getDouble($k){
		if(!isset($this->params[$k])){
			return doubleval(0);
		}
		return doubleval($this->params[$k]);
	}

	function getAz09($k){
		if(preg_match("/([^0-9a-z])/",strtolower($this->params[$k]))){
			return null;
		}else{
			return $this->params[$k];
		}
	}



	    /**  As of PHP 5.1.0  */
    public function __isset( $k ) {
        return isset( $this->params[$k] );
    }

    /**  As of PHP 5.1.0  */
    public function __unset( $k ) {
        unset( $this->params[$k]  );
    }



}


