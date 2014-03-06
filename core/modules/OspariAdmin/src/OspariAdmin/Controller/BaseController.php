<?php

namespace OspariAdmin\Controller;
use NZ\HttpRequest;
use NZ\HttpResponse;
class BaseController {
    
    protected $user;
    
    public function __construct( HttpRequest $req, HttpResponse $res ){
        $this->user = new \OspariAdmin\Model\User( $req->getSession()->user_id );
        //var_dump($_SESSION);exit(1);
        //$this->user = new \OspariAdmin\Model\User(1);
    }
    
    public function getUser(){
        return $this->user;
    }
    
    public function indexAction( HttpRequest $req, HttpResponse $res ){
        $user = $this->getUser();
        $map = new \NZ\Map();
        $map->user_id = $user->id;
        $pager = \OspariAdmin\Model\Draft::getPager($map, $req, $perPage = 20);
        
        $res->setViewVar('draftPager', $pager);
        $res->setViewVar('isWritable', $this->isUploadFolderWritable());
        $res->buildBody('index.php');
    }
    
    public function onPageNotFound( HttpRequest $req, HttpResponse $res ){
        $res->setStatusCode(404);
          $view = $res->getView();
          $body = $view->getPartialContent(__DIR__.'/../View/404.php');
        $res->buildBodyFromString($body);
    }
    
    private function isUploadFolderWritable(){
        $path = '/content/upload';
        $absolute_path = $_SERVER['DOCUMENT_ROOT'] . $path;
        if(is_writable($absolute_path)){
            return true;
        }
        return false;
    }
    
}