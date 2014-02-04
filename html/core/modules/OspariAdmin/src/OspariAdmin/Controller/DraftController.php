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

namespace OspariAdmin\Controller;

use NZ\HttpRequest;
use NZ\HttpResponse;

class DraftController extends BaseController {

     public function editAction(HttpRequest $req, HttpResponse $res) {
         return $this->createAction($req, $res);
     }
    public function createAction(HttpRequest $req, HttpResponse $res) {

        $view = $res->getView();
        $user = $this->getUser();
        $form = $this->createForm($view, $req);
        $jsonView = new \NZ\JsonView($view);

        if ($req->isPOST()) {
            try {
                $draft = $this->createOrEdit($form, $req, $user);

                $jsonView->set('draft_id', $draft->id);
                $jsonView->set('draft_url', $draft->getUrl());
                if ($draft->isPublished()) {
                    $jsonView->setSuccessMessage('Awsome! Your post has been published');
                     $jsonView->set('post_url', $draft->getUrl());
                    $jsonView->set('published', 1);
                }else{
                    $jsonView->setSuccessMessage('Your post has been saved as draft.');
                }
                
                return $res->sendJson($jsonView->render());
                
            } catch (\Exception $exc) {
                return $res->sendErrorMessageJSON($exc->__toString());
                //$res->setViewVar('Exception', $exc);
            }
        }
        
        if( $draft_id = $req->getInt('draft_id') ){
            $draft = new \OspariAdmin\Model\Draft( $draft_id );
            $req = $draft->toHttpRequest($req);
            $form = $this->createForm($view, $req);
        }
        
        

        $res->setViewVar('uploadURL', OSPARI_URL.'/'.OSPARI_ADMIN_PATH.'/media/upload');
        $res->setViewVar('req', $req);
        $res->setViewVar('form', $form);
        $res->buildBody('Draft/create.php');
    }

    public function autoSaveAction(HttpRequest $req, HttpResponse $res) {
        $view = $res->getView();
        $user = $this->getUser();
        $form = $this->createForm($view, $req);

        $jsonView = new \NZ\JsonView($view);

        if ($req->isPOST()) {
            try {
                $draft = $this->createOrEdit($form, $req, $user);

                $jsonView->setSuccessMessage('Auto saved on.' . $draft->edited_at . '.');
                $jsonView->set('draft_id', $draft->id);
                $jsonView->set('draft_slug', $draft->slug);
                $jsonView->set('draft_url', $draft->getUrl());

                return $res->sendJson($jsonView->render());
            } catch (\Exception $exc) {
                return $res->sendErrorMessageJSON($exc->__toString());

                return $res->sendErrorMessageJSON($exc->getMessage());
            }
        } else {
            $res->sendErrorMessageJSON('Post method required');
        }
    }

    private function createOrEdit(\NZ\BootstrapForm $form, $req, $user) {


        $model = new \OspariAdmin\Model\Draft($req->getInt('draft_id'));
        
        $model->state = $req->getInt('state');
        
        if (!$model->id) {
            $model->setCreatedAt();
        }
        
         if (!$model->slug ) {
            $nzUri = new \NZ\Uri();
            $slug = $nzUri->slugify($req->title);
            
            $model->slug = $this->createSlug($slug);
          }

        if ($req->state == \OspariAdmin\Model\Draft::STATE_PUBLISHED) {
            $model->state = \OspariAdmin\Model\Draft::STATE_PUBLISHED;
            $model->setDateTime('published_at', new \DateTime());
        }

        //$model = $form->saveToModel($model);
        $model->title = $req->title;
        $model->user_id = $user->id;
        $model->content = $req->content;
        $model->tags = $req->tags;
        $model->setDateTime('edited_at', new \DateTime());
        $model->save();

        if ($req->state == \OspariAdmin\Model\Draft::STATE_PUBLISHED) {
            
            $post = \OspariAdmin\Model\Post::findOne( array('draft_id' => $model->id ) );
            $modelArray = $model->toArray();
            unset($modelArray['id']);
            if( !$post ){
                $post = new \OspariAdmin\Model\Post();
                $post->draft_id = $model->id;
            }

            foreach ($modelArray as $k => $v) {
                $post->set($k, $v);
            }
            $post->save();
           
        }
        return $model;
    }

    
    private function createSlug( $slug, $try = 0 ){
        if( !$slug ){
            $slug = 'post';
        }
        
        $draft = \OspariAdmin\Model\Draft::findOne( array( 'slug' => $slug ) );
        if( $draft ){
            $try++;
            $slug = $slug.'-'.$try;
            return $this->createSlug($slug, $try);
        }
        return $slug;
        
        
    }

        private function validateForm(\NZ\BootstrapForm $form, \NZ\HttpRequest $req) {
        if (!$form->validate($req)) {
            throw new \Exception('Please fill all required fields');
        }

        return TRUE;
    }

    private function createForm($view, \NZ\HttpRequest $req) {
        $form = new \NZ\BootstrapForm($view, $req);
        $form->setCssClass('form-horizontal');
        $form->createElement('title')
                ->setAttribute('placeholder', 'title')
                ->setRequired();
        $form->createElement('content')
                ->toTexArea()
                ->setAttribute('rows', 20)
                ->setAttribute('id', 'draft-content-textarea');

         
         $form->createElement('cover')
                ->setAttribute('placeholder', 'Cover Image') ;
                
        
        $form->createElement('tags')
                ->setAttribute('autocomplete', 'off')
                ->setAttribute('placeholder', 'Comma sperated')
                ->setAttribute('id', 'tag-input')
                ->setRequired();
        $form->createHiddenElement('state', 'draft', 'post-state-input');
        return $form;
    }

}
