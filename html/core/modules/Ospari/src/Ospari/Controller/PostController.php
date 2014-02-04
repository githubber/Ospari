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


        $res->setViewVar('posts', $posts);

        $res->setViewVar('defaultContent', $helper->getDefaultContent());
        $res->setViewVar('indexContent', $helper->getIndexContent());

        $res->buildBody('index.php');
    }

    public function viewAction(HttpRequest $req, HttpResponse $res) {

        $post = \Ospari\Model\Post::findOne(array('slug' => $req->get('slug')));
        if (!$post) {
            $res->setStatusCode(404);
            return $res->buildBodyFromString('Page not found');
        }

        $res->setViewVar('post', $post->toStdObject());

        $helper = new \Ospari\Helper\Theme();
        $helper->prepare();

        $this->setGlobalVars($res);

        $res->setViewVar('defaultContent', $helper->getDefaultContent());
        $res->setViewVar('postContent', $helper->getPostContent());

        $res->buildBody('post.php');
    }

}
