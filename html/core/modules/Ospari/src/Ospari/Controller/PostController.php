<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DefaultController
 *
 * @author fon-pah
 */

namespace Ospari\Controller;

use NZ\HttpRequest;
use NZ\HttpResponse;

class PostController extends BaseController {

    public function indexAction(HttpRequest $req, HttpResponse $res) {
        
        $user = $this->getUser();
        
        $helper = new \Ospari\Helper\Theme();
        $helper->prepare();

        //var_dump($helper->getDefaultContent());
        $this->setGlobalVars($res);


        $map = new \NZ\Map();
        
        $postPager = \Ospari\Model\Post::getPager($map, $req);
        
        $posts = array();
        foreach ($postPager->getItems() as $post) {
            $posts[] = $post->toStdObject();
        }

        $pagination = $postPager->toPagination();



        $paginationContent = $helper->getPaginationContent();
        $paginationContent = str_replace('{{pageUrl prev}}', OSPARI_URL . '/page/' . $pagination->getPrevious(), $paginationContent);
        $paginationContent = str_replace('{{pageUrl next}}', OSPARI_URL . '/page/' . $pagination->getNext(), $paginationContent);
        $paginationContent = str_replace('{{page}}', $req->getInt('page'), $paginationContent);
        $paginationContent = str_replace('{{pages}}', $postPager->count(), $paginationContent);

        $res->setViewVar('pagination', $pagination);
        $res->setViewVar('posts', $posts);

        $res->setViewVar('paginationContent', $paginationContent);
        $res->setViewVar('defaultContent', $helper->getDefaultContent());
        $res->setViewVar('indexContent', $helper->getIndexContent());

        $res->buildBody('index.php');
    }

    public function viewAction(HttpRequest $req, HttpResponse $res) {

        try{
            $post = $this->getPostObject($req, $res);
        } catch (\Exception $ex) {
             $res->setStatusCode(404);
            return $res->buildBodyFromString($ex->getMessage());

        }

        $res->setViewVar('post', $post->toStdObject());

        $helper = new \Ospari\Helper\Theme();
        $helper->prepare();

        $this->setGlobalVars($res);

        $res->setViewVar('defaultContent', $helper->getDefaultContent());
        $res->setViewVar('postContent', $helper->getPostContent());

        $res->buildBody('post.php');
    }

    protected function getPostObject(HttpRequest $req, HttpResponse $res) {
        if ($slug = $req->get('slug')) {
            $post = \Ospari\Model\Post::findOne(array('slug' => $slug ));
            if( $post ){
                return $post;
            }
           
        }
        
        if( !$draft_id = $req->getInt('draft_id') ){
            throw new \Exception('Page not found');
        }
        
        /// user must be logged for preview
        $user = $this->getUser();
        if( !$user->id ){
            throw new \Exception('Permission denied');
        }
        
        $draft = \Ospari\Model\Draft::findOne(array('id' => $draft_id));
        if( !$draft ){
             throw new \Exception('Draft not found');
        }
        
        $post = new \Ospari\Model\Post();
        foreach( $draft->toArray() as $k => $v ){
            $post->set($k, $v);
        }
        return $post;
    }

}
