<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ControllerContainer
 *
 * @author 28h
 */

namespace NZ;

class ControllerContainer {
    
    protected $nzSession;
    protected $nzRequest;
    protected $nzResponse;
    protected $nzView;
    
    protected $nzConfig;
    
    public function setNZ_Config( \NZ\Config $conf ) {
        $this->nzConfig = $conf;
    }
    
    public function setRequest( $req ){
        $this->nzRequest = $req;
    }
    
    /**
     * 
     * @return \NZ\HttpRequest
     */
    public function getRequest(){
        return $this->nzRequest;
    }
    
    public function setResponse($res) {
        $this->nzResponse = $res;
    }

    /**
     * 
     * @return \NZ\HttpResponse
     */
    public function getResponse() {
        return $this->nzResponse;
    }





    /**
     * 
     * @return \NZ\Config
     */
    public function getNZ_Config() {
        return $this->nzConfig;
    }
    
    public function setSession($session){
        $this->nzSession = $session;
    }
    
    public function getSession(){
        return $this->nzSession;
    }
    
}

