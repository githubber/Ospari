<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BootstrapPillNavigation
 *
 * @author manuelschoebel
 */
namespace NZ;

class BootstrapNavs {
    //put your code here
    
    const NAV_TABS = 'nav-tabs';
    const NAV_PILLS = 'nav-pills';
    const NAV_LIST = 'nav-list';
    const NAV_TYPE_STACKED = 'nav-stacked';
    
    private $view;
    private $navigationElements;
    private $templateName = "BootstrapPillNavigationTemplate.php";
    private $navClasses = array('nav-pills');
    
    /**
     * Adds html content navigation to $view->contentNavigationPills
     * @param type $view
     * @param type $navElements
     */
    public function __construct(\NZ\View $view) {
        $this->view = $view;
    }
    
    private function getTemplatePath(){
        return __DIR__."/templates/".$this->templateName;
    }
    
    public function setTemplateName($templateName){
        $this->templateName = $templateName;
    }
    
    public function setCssClass($class){
        $this->navClasses = array($class);
    }
    
    public function addCssClass($class){
        $this->navClasses[] = $class;
    }
    
    /**
     * Param is array('/your/url' => 'Link Name', '/another-url' => 'Another Link');
     * @param type $navigationElements
     */
    public function setNavigationElements($navigationElements){
        $this->navigationElements = $navigationElements;
    }
    
    /**
     * Needs current url path to set the right link active
     * @param type $currentUrlPath
     */
    public function getHtml($currentUrlPath = ''){
        $this->view->navigationElements = $this->navigationElements;
        $this->view->currentUrlPath = $currentUrlPath;
        $this->view->bootstrapNavsClasses = $this->navClasses;
        return $this->view->getContent($this->getTemplatePath());        
    }   
    
}

?>
