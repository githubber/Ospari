<?php

namespace Ospari\Controller;
use NZ\HttpRequest;
use NZ\HttpResponse;

class BaseController {
    
    protected $user;
    
    public function createAction( HttpRequest $req, HttpResponse $res ){
        $this->user = new \Ospari\Model\User( $req->getSession()->user_id );

        /*
        $blog = new \stdClass();
        $blog->logo = '/html/upload/ospari-logo.png';
        $blog->description = 'Ospari Blog';
        $blog->title = 'Post by the dev team';
        $blog->cover = '/html/upload/blog-cover.jpg';
        $blog->url = '/html';
         * 
         */

    }
    
    public function setGlobalVars( $res ){
        $blog = \OspariAdmin\Model\Setting::getAsStdObject();
        unset($blog->email);
        $blog->url = OSPARI_URL;
        $res->setViewVar('blog',  $blog);
    }

        public function getUser(){
        return $this->user;
    }
    
}