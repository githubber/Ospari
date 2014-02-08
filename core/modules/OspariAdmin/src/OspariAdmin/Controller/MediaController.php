<?php

namespace OspariAdmin\Controller;

use NZ\HttpRequest;
use NZ\HttpResponse;

class MediaController extends BaseController {

    public function uploadAction(HttpRequest $req, HttpResponse $res) {


        if ($req->isPOST()) {
            $where = array('uplouded' => 0);
            $media = \OspariAdmin\Model\Media::findOne($where);
            if (!$media) {
                $media = new \OspariAdmin\Model\Media();
                $media->name = $req->name;
                $media->save();
            }

            try {
                $media = $this->handleUpload($media);
            } catch (\Exception $ex) {
                $res->setViewVar('exception', $ex);
            }

            $media->setCreatedAt();
            $media->save();
            $res->setViewVar('media', $media);
            
        }
        
        return $res->buildBody('media/upload.php');
        
    }

    public function editAction(HttpRequest $req, HttpResponse $res) {
        
    }

    private function handleUpload($media) {
        if (!isset($_FILES['file'])) {
            throw new \Exception('No file selected');
            ;
        }

        if (isset($_FILES['file']['name'])) {
            $fileName = $_FILES['file']['name'];
        }

        if (!$media->name) {
            $media->name = $fileName;
        }

        $nzUri = new \NZ\Uri();
        $slug = $nzUri->slugify($media->name);


        $uploadPath = OSPARI_PATH . '/upload';

        $fileHandler = new \NZ\Filehandler();
        $mediaPath = $fileHandler->generatePathFromID($media->id);

        $path = $uploadPath . $mediaPath;
        $path = str_replace('//', '/', $path);
        $fileHandler->makeDirs($path);

        $large = "/{$slug}-{$media->id}";
        $thumb = "/{$slug}-{$media->id}-thumb";

        $src = \NZ\Image::tryUpload('file');
        $fileHnadler = new \NZ\Filehandler();
        //$nzFile->makeDirs($strPath);


        $fileHnadler->copyFile($src, $path . $large);

        $image = new \NZ\Image($src);
        $image->scale(160, 160);
        $image->save($path . $thumb);

        $media->large = $large;
        $media->thumb = $thumb;

        return $media;
    }

}
