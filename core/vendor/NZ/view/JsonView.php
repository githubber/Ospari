<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace NZ;

Class JsonView{
    
    private $view;
    private $response;
    
    /**
     * Has response->success = true on default.
     * @param \NZ\View $view
     */
    public function __construct(View $view) {
        $this->view = $view;
        $this->response = new \stdClass();
        $this->response->success = true; // true unless setErrorMessage
    }
    
    public function setErrorMessage($msg){
        $this->response->success = false;
        $this->set('message', $msg);
    }
    
    public function setErrorMessages($msgArr){
        $this->response->success = false;
        $this->set('errorMessages', $msgArr);
    }
    
    public function setSuccessMessage($msg){
        $this->response->success = true;
        $this->set('message', $msg);
    }
    
    public function set($k, $v){        
        $this->response->$k = $v;
    }
    
    /**
     * Sets html into response->html.
     * @param type $html
     */
    public function setHtml($html){
        $this->set('html', $html);
    }
    
    public function setResponse($object){
        $this->response = $object;
    }
    
    public function render(){
        return json_encode($this->response);
    }
}
