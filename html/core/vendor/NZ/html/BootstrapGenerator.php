<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BootstrapGenerator
 *
 * @author manuelschoebel
 */
namespace NZ;
class BootstrapGenerator {
    //put your code here
    const FIELD_TEMPLATE = 'template';
    
    const BASE_TEMPLATE_SELECT = 'select.php';
    const BASE_TEMPLATE_CHECKBOX = 'checkbox.php';
    const BASE_TEMPLATE_HIDDEN = 'hidden.php';
    
    const FIELD_DATA = 'bootstrap_generator_data';
    
    protected $basicTranslations = array();
    
    protected function getBaseTemplatePath(){
        return __DIR__."/templates/basic/";
    }
    
    public function getTranslation($key){
        if(isset($this->basicTranslations[$key])){
            return $this->basicTranslations[$key];
        }
        return '';
    }
    
    public function setTranslation($key, $value){
        $this->basicTranslations[$key] = $value;
    }
    
}

?>