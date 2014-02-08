<?php

namespace Ospari\Controller;

use NZ\HttpRequest;
use NZ\HttpResponse;

class AssetController extends BaseController {

    public function cssAction(HttpRequest $req, HttpResponse $res) {
        $res->setContentType('text/css');
        return $this->readFile($req, $res, 'css');
    }

    public function javaScriptAction(HttpRequest $req, HttpResponse $res) {

        return $this->readFile($req, $res, 'js');
    }

    private function readFile(HttpRequest $req, HttpResponse $res, $ext = 'css') {
        $theme = new \Ospari\Helper\Theme();
        $cssFile = $req->getRouter('css_file');
        $cssFile = str_replace('..', '', $cssFile);

        $filePath = $theme->getPath() . '/assets/' . $ext . '/' . $cssFile . '.' . $ext;
        if (!file_exists($filePath)) {
             $res->setStatusCode(404);
             return $res->buildBodyFromString('file not found');
        }

        $file = $filePath;
        $last_modified_time = filemtime($file);
        $etag = md5_file($file);

        header("Last-Modified: " . gmdate("D, d M Y H:i:s", $last_modified_time) . " GMT");
        header("Etag: $etag");

        if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
            if (strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == $last_modified_time) {
                //no 04 Not Modified
                return $res->setStatusCode(304);
            }
        }

        if (isset($_SERVER['HTTP_IF_NONE_MATCH'])) {
            if (trim($_SERVER['HTTP_IF_NONE_MATCH']) == $etag) {
                //no 04 Not Modified
                return $res->setStatusCode(304);
            }
        }

         $string = file_get_contents($filePath);

        $res->buildBodyFromString($string);
    }

}
