<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace NZ;
class BootstrapTableHeader extends BootstrapGenerator{
    const TABLE_HEADER_TEMPLATE = 'TableHeader.php';
    const DROP_DOWN_SORT_HEADER_TEMPLATE = 'DropDownSortHeader.php';
    const SIMPLE_HEADER_TEMPLATE = 'SimpleHeader.php';
    const TEMPLATE_MULTIPLE_DROP_DOWN_SORT_HEADER = 'DropDownMultipleSortHeader.php';
    const FIELD_TEMPLATE = 'template';
    
    const FIELD_NAME = 'name';
    const FIELD_FIELD = 'field';
    const FIELD_URI_DESC = 'uri_desc';
    const FIELD_URI_ASC = 'uri_asc';
    const FIELD_FIELDS = 'fields';
    const ORDER_BY = 'order_by';
    const ORDER_TYPE = 'order_type';
    const ORDER_TYPE_ASC = 'ASC';
    const ORDER_TYPE_DESC = 'DESC';
    
    private $view;
    private $headers = array();
    private $lang;
    
    
    /**
     * array('url' => array(), 'follower' => array(')
     */
    public function __construct(\NZ\View $view, $lang=null){
        $this->view = $view;
        $this->lang = $lang;
        $this->setBasicTranslations();        
    }
    
    private function setBasicTranslations(){
        if($this->lang){
            $this->basicTranslations['ascending'] = $this->lang->ascending;
            $this->basicTranslations['descending'] = $this->lang->descending;
        } else{
            $this->basicTranslations['ascending'] = 'ascending';
            $this->basicTranslations['descending'] = 'descending';
        }
    }
    
    private function getTemplatePath(){
        return __DIR__."/templates/BootstrapTableHeader/";
    }
    
    public function addSimpleHeader($val){
        $header = array();
        $header[self::FIELD_TEMPLATE] = $this->getTemplatePath().self::SIMPLE_HEADER_TEMPLATE;
        $header[self::FIELD_DATA] = $val;
        $this->headers[] = $header;
        return $this;
    }
    
    public function addDropDownSortHeader($name, $field, $currentUrl){
        $data = array(self::FIELD_NAME => $name, self::FIELD_FIELD => $field);
        
        $nzUri = new \NZ\Uri($currentUrl);
        $nzUri->removeParam(self::ORDER_BY);
        $nzUri->removeParam(self::ORDER_TYPE);
        $nzUri->addParam(self::ORDER_BY, $field);
        $nzUri->addParam(self::ORDER_TYPE, self::ORDER_TYPE_ASC);
        $data[self::FIELD_URI_ASC] = $nzUri->__toString();
        
        $nzUri->removeParam(self::ORDER_TYPE);
        $nzUri->addParam(self::ORDER_TYPE, self::ORDER_TYPE_DESC);
        $data[self::FIELD_URI_DESC] = $nzUri->__toString();
        
        $header = array();
        $header[self::FIELD_TEMPLATE] = $this->getTemplatePath().self::DROP_DOWN_SORT_HEADER_TEMPLATE;
        $header[self::FIELD_DATA] = $data;
        $this->headers[] = $header;
        return $this;
    }
    
    public function addDropDownMultipleSortHeader($name, $fields, $currentUrl){
        $data = array(self::FIELD_NAME => $name, self::FIELD_FIELDS => array());
                
        $nzUri = new \NZ\Uri($currentUrl);
        $nzUri = $this->removeOrderParams($nzUri);
        
        foreach($fields as $fname => $fvalue){
            $fieldData = array();
            
            $nzUri = $this->addOrderParams($nzUri, $fvalue, self::ORDER_TYPE_ASC);            
            $fieldData[self::FIELD_NAME] = $fname;
            $fieldData[self::FIELD_URI_ASC] = $nzUri->__toString();
            
            $nzUri = $this->removeOrderParams($nzUri);
            $nzUri = $this->addOrderParams($nzUri, $fvalue, self::ORDER_TYPE_DESC);
            $fieldData[self::FIELD_URI_DESC] = $nzUri->__toString();
            
            $data[self::FIELD_FIELDS][] = $fieldData;
        }
        
        $this->addHeader($this->getTemplatePath().self::TEMPLATE_MULTIPLE_DROP_DOWN_SORT_HEADER, $data);
        return $this;
    }
    
    private function addHeader($template, $data){
        $header = array();
        $header[self::FIELD_TEMPLATE] = $template;
        $header[self::FIELD_DATA] = $data;
        $this->headers[] = $header;
    }
    
    private function addOrderParams($nzUri, $field, $type){
        $nzUri->addParam(self::ORDER_BY, $field);
        $nzUri->addParam(self::ORDER_TYPE, $type);
        return $nzUri;
    }
    
    private function removeOrderParams($nzUri){
        $nzUri->removeParam(self::ORDER_BY);
        $nzUri->removeParam(self::ORDER_TYPE);
        return $nzUri;
    }
    
    /**
     * array('url', 'Follower' => 'follower', 'Price' => 'price', 'Fans / Follower' => array('fans', 'follower')) -> 
     * 
     */
    private function createHeadItem(){
                
    }
    
    public function getHtml(){
        $this->view->headers = $this->headers;
        $this->view->bootstrapGenerator = $this;
        return $this->view->getContent($this->getTemplatePath().self::TABLE_HEADER_TEMPLATE);
    }
    
    
}
?>
