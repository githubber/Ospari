<?php

namespace OspariAdmin\Controller;
use NZ\HttpRequest;
use NZ\HttpResponse;
class BaseController {
    
    protected $user;
    
    public function __construct( HttpRequest $req, HttpResponse $res ){
        $this->user = new \OspariAdmin\Model\User( $req->getSession()->user_id );
        $this->user = new \OspariAdmin\Model\User(1);
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
        $res->buildBody('index.php');
    }
    
    public function onPageNotFound( HttpRequest $req, HttpResponse $res ){
        $res->setStatusCode(404);
          $view = $res->getView();
          $body = $view->getPartialContent(__DIR__.'/../View/404.php');
        $res->buildBodyFromString($body);
    }
    
}