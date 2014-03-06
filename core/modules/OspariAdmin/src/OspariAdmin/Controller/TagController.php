<?php

namespace OspariAdmin\Controller;

/**
 * Description of TagController
 *
 * @author fon-pah
 */
use NZ\HttpRequest;
use NZ\HttpResponse;
use OspariAdmin\Model\Tag;
use NZ\Uri;
use OspariAdmin\Model\Tag2Draft;
use NZ\JsonView;
class TagController extends BaseController{
    public function listAction( HttpRequest $req, HttpResponse $res ){
        if(!$req->isAjax()){
            return $res->sendErrorMessage('Bad Request!');
        }

        $json = new JsonView($res->getView());
        $json->setResponse(Tag::getTags());
        return $res->sendJson($json->render());
    }
    
    public function addAction( HttpRequest $req, HttpResponse $res ){
        if(!$req->isAjax()){
            return $res->sendErrorMessage('Bad Request!');
        }
        $word = $req->get('tag');
        if(!$word){
            return $res->sendErrorMessageJSON('Tag is required!');
        }
        $tag = new Tag(array('word'=>$word));
        
        try {
            if(!$tag->id){
                $uri = new Uri();
                $tag->word =$word;
                $tag->slug = $uri->slugify($word);
                $tag->setCreatedAt();
                $tag->save();           
            }
            $draft_id = $req->getInt('draft_id');
            if($draft_id){
                $tag2draft = new Tag2Draft(array('draft_id'=>$draft_id,'tag_id'=>$tag->id));
                if(!$tag2draft->id){
                    $tag2draft->draft_id = $draft_id;
                    $tag2draft->tag_id= $tag->id;
                    $tag2draft->save();
                }
            }
        } catch (\Exception $exc) {
            return $res->sendErrorMessage($exc->getMessage());
        }

        
        return $res->sendSuccessMessageJSON('ok');
    }
    
    public function deleteAction( HttpRequest $req, HttpResponse $res ){
        if(!$req->isAjax()){
            return $res->sendErrorMessage('Bad Request!');
        }
        $draft_id = $req->getInt('draft_id');
        if(!$draft_id){
          return $res->sendErrorMessageJSON('Invalid Draft ID');
        }
        $word = $req->get('tag');
        if(!$word){
            return $res->sendErrorMessageJSON('Tag is required!');
        }
        $tag = Tag::findOne(array('word'=>$word));
        if(!$tag){
            return $res->sendErrorMessageJSON('Tag not found');
        }
        $tag2draft = new Tag2Draft(array('draft_id'=>$draft_id,'tag_id'=>$tag->id));
        try {
            $tag2draft->delete(null);
        } catch (\Exception $exc) {
            return $res->sendErrorMessage($exc->getMessage());
        }

        return $res->sendSuccessMessageJSON('ok');      
        
    }
    
}
