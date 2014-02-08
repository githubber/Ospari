<?php

namespace Ospari\Controller;

use NZ\HttpRequest;
use NZ\HttpResponse;

class BaseController {

    protected $user;

    public function __construct(HttpRequest $req, HttpResponse $res) {
        $this->user = new \Ospari\Model\User($req->getSession()->user_id);

    }

    public function setGlobalVars($res) {
        $blog = \OspariAdmin\Model\Setting::getAsStdObject();
        unset($blog->email);
        $blog->url = OSPARI_URL;
        $res->setViewVar('blog', $blog);
    }

    public function getUser() {
        return $this->user;
    }

}
