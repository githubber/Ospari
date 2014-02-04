<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BootstrapFilterForm
 *
 * @author manuelschoebel
 */
namespace NZ;
class BootstrapFilterForm extends BootstrapGenerator{
    //put your code here
    
    const FILTER_BUTTON_LABEL = 'btnLabel';
    
    const FILTER_FORM_TEMPLATE = 'FilterForm.php';
    const DROP_DOWN_TEMPLATE = 'DropDown.php';
    
    const FIELD_FIELD = 'fieldField';    
    const FIELD_FILTERS = 'bootstrap_table_filters';
    const FIELD_VALUES = 'fieldValues';
    const FIELD_SELECTED = 'selected';
    const FIELD_LABEL = 'label';
    const FIELD_CSS_CLASS = 'cssClass';
    const SUBMIT_ON_CHANGE = 'submitOnChange';


    private $filters = array();
    private $cssClasses = array();
    private $setShowFilterBtn = true;
    
    public function __construct(\NZ\View $view, $btnLabel='filter'){
        $this->view = $view;
        $filterBtnLabel = self::FILTER_BUTTON_LABEL;
        $this->view->$filterBtnLabel = $btnLabel;
    }
    
    private function getTemplatePath(){
        return __DIR__."/templates/BootstrapFilterForm/";
    }
    
    public function addCssClass($class){
        $this->cssClasses[] = $class;        
    }
    
    public function addDropDownFilter(\NZ\Map $nzMap, $field, $values, $selected='', $label){
        $data = array();
        $data[self::FIELD_FIELD] = $field;
        $data[self::FIELD_VALUES] = $values;
        $data[self::FIELD_SELECTED] = $selected;        
        $data[self::FIELD_LABEL] = $label;
        $data[self::FIELD_CSS_CLASS] = ($nzMap->isParam(self::FIELD_CSS_CLASS)) ? $nzMap->get(self::FIELD_CSS_CLASS) : '';
        if(isset($nzMap->submitOnChange)){
            $data[self::SUBMIT_ON_CHANGE] = true;
        }
        
        $filter = array();
        $filter[self::FIELD_TEMPLATE] = $this->getBaseTemplatePath().self::BASE_TEMPLATE_SELECT;
        $filter[self::FIELD_DATA] = $data;
        
        $this->filters[] = $filter;
        
        return $this;
    }
    
    public function addCheckBoxFilter(\NZ\Map $nzMap, $field, $checked, $label){
        $data = array();
        $data[self::FIELD_FIELD] = $field;
        $data[self::FIELD_SELECTED] = $checked;
        $data[self::FIELD_LABEL] = $label;
        
        $filter = array();
        $filter[self::FIELD_TEMPLATE] = $this->getBaseTemplatePath().self::BASE_TEMPLATE_CHECKBOX;
        $filter[self::FIELD_DATA] = $data;
        $this->filters[] = $filter;
        
        return $this;
    }
    

    public function addHiddenField($name, $value){
    $data = array();
    $data[self::FIELD_FIELD] = $name;
    $data[self::FIELD_VALUES] = $value;

    $filter = array();
    $filter[self::FIELD_TEMPLATE] = $this->getBaseTemplatePath().self::BASE_TEMPLATE_HIDDEN;
    $filter[self::FIELD_DATA] = $data;
    $this->filters[] = $filter;

    return $this;
    }
    
    public function setShowFilterBtn($show){
        $this->setShowFilterBtn = $show;
    }
    
    public function getHtml(){
        //$filterField = self::
        $this->view->bootstrap_table_filters = $this->filters;
        $this->view->cssClasses = implode(' ', $this->cssClasses);
        $this->view->setShowFilterBtn = $this->setShowFilterBtn;
        return $this->view->getContent($this->getTemplatePath().self::FILTER_FORM_TEMPLATE);
    }
}

?>