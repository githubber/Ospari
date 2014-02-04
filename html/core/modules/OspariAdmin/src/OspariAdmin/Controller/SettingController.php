<?php

namespace OspariAdmin\Controller;

use NZ\HttpRequest;
use NZ\HttpResponse;

class SettingController extends BaseController {
    
    public function editAction(HttpRequest $req, HttpResponse $res) {
        
         $view = $res->getView();
         $setting = new \OspariAdmin\Model\Setting();
         if ($req->isPOST()) {
            try {
                $form = $this->createForm($view, $req);
                $form->saveToModel( $setting );
                
                return $res->sendSuccessMessageJSON('Settings successfully saved');
            } catch (\Exception $ex) {
                
                return $res->sendErrorMessageJSON($ex->getMessage());
            }
        }
        
        
         $req = $setting->toHttpRequest($req);
         
         
         
        $form = $this->createForm($view, $req);
        $res->setViewVar('form', $form);
        return $res->buildBody('setting/edit.php');
    }
    
    /**
     * 
     * @param \NZ\View $view
     * @param \NZ\HttpRequest $req
     * @return \NZ\BootstrapForm
     */
    private function createForm(\NZ\View $view, HttpRequest $req) {

        $form = new \NZ\BootstrapForm($view, $req);
        $form->setID('setting-edit-form');
        $form->setCssClass('form-horizontal');
        $form->addSubmitClass('btn btn-primary');

        $form->createElement('title')
                ->setLabelText('Blog Title')
                ->setRequired()
                ;

        $form->createElement('description')
                ->setLabelText('Blog Description')
                ->toTexArea();
                

        $form->createElement('logo')
                ->setLabelText('Blog Logo')
                ;
        
        $form->createElement('cover')
                ->setLabelText('Blog Cover')
                ;
        
        $form->createElement('email')
                ->setLabelText('Email Address')
                ->setType('email')
                ->setRequired();

         $form->createElement('perpage')
                 ->setType('number')
                ->setLabelText('Posts per page')
                ;
        
        $form->createElement('theme')
                ->setLabelText('Theme')
                ->toSelect( $this->getThemes() )
                ;

        return $form;
    }
    
    protected function getThemes(){
        $path = OSPARI_PATH.'/content/themes';
        $fileHandler = new \NZ\Filehandler();
        $ret = array();
        
        foreach( $fileHandler->getDirs($path, FALSE) as $v ){
            $baseName = basename( $v );
            $ret[$baseName] = $baseName;
        }
        return $ret;
    }
    
}