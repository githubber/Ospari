<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace OspariAdmin\Controller;

/**
 * Description of MediaController
 *
 * @author fon-pah
 */
use NZ\HttpRequest;
use NZ\HttpResponse;
use OspariAdmin\Model\Media;
use OspariAdmin\Model\Draft2Media;
use OspariAdmin\Model\Draft;
use \NZ\Filehandler;
use \NZ\Image;
use \NZ\Uri;
class MediaController extends BaseController {
     public function uploadAction( HttpRequest $req, HttpResponse $res){
        $id = $req->getInt('draft_id');
        if(!$id){
            return $res->sendErrorMessageJSON('Invalid Draft Identifier');
        }
        $draft = Draft::findOne(array('id'=>$id));
        if(!$draft){
            return $res->sendErrorMessageJSON('Draft could be found!');
        }
        try {
            
            if ($req->hasUpload()) {
                 $media = $this->handleUpload($req);
                 $draft2media = new Draft2Media(array('draft_id'=>$id,'media_id'=>$media->id));
                 if( !$draft2media->id ){
                      $draft2media->draft_id = $id;
                      $draft2media->media_id = $media->id;
                      $draft2media->save();
                 }
                 $draft->thumb = $media->thumb;
                 $draft->media_id = $media->id;
                 $draft->save();
           }
        } catch (\Exception $exc) {
            return $res->sendErrorMessageJSON($exc->getMessage());
        }
        return $res->sendSuccessMessageJSON(OSPARI_URL.'/content/upload'.$media->large);
    }
    
    private function handleUpload(HttpRequest $req){
        $fh = new Filehandler();
        $subPath = '/'.date('Y').'/'. date('m');
        $path = '/content/upload' .$subPath;
        $absolute_path = $_SERVER['DOCUMENT_ROOT'] . $path;
        $fh->makeDirs($absolute_path);

        $src = Image::tryUpload('image');
        $img = new Image($src);
        $uri = new Uri();
        $name = $_FILES['image']['name'];
        
        $slug = $uri->slugify($this->removeExtension($name));

        $large = $subPath.'/'.$slug . '.' . $img->getExtension();
        $img->save($absolute_path . '/' . $slug . '.' . $img->getExtension());

        $img->scale('300', '300');
        $img->save($absolute_path . '/' . $slug . '-300x300.' . $img->getExtension());
        $thumb = $subPath.'/'.$slug . '-300x300.' . $img->getExtension();
        
        $media = new Media(array('large'=>$large,'thumb'=>$thumb,'user_id'=>  $this->getUser()->id));
        
        if(!$media->id){
            $media->setCreatedAt();
            $media->user_id = $this->getUser()->id;
        }
        
        $media->large = $large;
        $media->thumb = $thumb;
        $media->ext = $img->getExtension();
        $media->save();
        return $media;
    }
    
    private function removeExtension( $filename ){
        $arr = explode('.', $filename);
        if(count($arr)>1){
            array_pop($arr);
        }
        $str = implode('', $arr);
        return $str;
    }
    
}
