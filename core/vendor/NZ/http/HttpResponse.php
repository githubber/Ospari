<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of HttpResponse
 *
 * @author 28h
 */

namespace NZ;

class HttpResponse {

    protected $module;

    /**
     *
     * @var \NZ\View;
     */
    protected $nzView;
    protected $body;
    protected $conf;

    public function __construct() {
        $this->init();
    }

    public function init() {

        $conf = Config::getInstance();
        $this->conf = $conf;

        $view = \NZ\View::getInstance();
        $view->head = $conf->headTPL;
        $view->tail = $conf->tailTPL;


        $this->nzView = $view;
    }

    /**
     * 
     * @return \NZ\View
     */
    public function getView() {
        return $this->nzView;
    }

    public function setView(\NZ\View $view) {

        $this->view = $view;
    }

    /**
     * 
     * @param type $module
     * @return \NZ\HttpResponse
     */
    public function setModule($module) {
        $module->init($this->getContainer());
        $this->module = $module;
        $this->init();
        return $this;
    }

    protected function getContainer() {
        $cont = new \NZ\ControllerContainer();
        $cont->setNZ_Config($this->conf);
        $cont->setRequest(HttpRequest::getInstance());
        $cont->setResponse($this);
        $cont->setSession(SessionHandler::getInstance());
        return $cont;
    }

    /**
     * 
     * @return type
     */
    public function getModule() {
        return $this->module;
    }

    public function redirect($location, $code = 302) {
        if (headers_sent()) {
            return '<a href="' . $location . '">' . $location . '</a>';
        }

        header('location: ' . $location, TRUE, $code);
        exit(1);
    }

    public function getViewContent($path) {
        return $this->nzView->getContent($this->module->getViewPath() . '/' . $path);
    }
    
    public function getPartialViewContent($path) {
        return $this->nzView->getContent($this->module->getViewPath() . '/' . $path);
    }
    
    
    

    /**
     * 
     * @param type $msg
     * @param type $isAjax
     * @return type
     */
    public function sendErrorMessage($msg) {
        $this->buildBodyFromString($this->nzView->renderError($msg));
    }

    /**
     * 
     * @param type $msg
     * @return type
     */
    public function sendErrorMessageJSON($msg) {
        $jsonView = new JsonView($this->nzView);
        $jsonView->setErrorMessage($msg);
        return $this->sendJson($jsonView->render());
    }

    /**
     * 
     * @param type $msg
     * @return type
     */
    public function sendSuccessMessageJSON($msg) {
        $jsonView = new JsonView($this->nzView);
        $jsonView->setSuccessMessage($msg);
        return $this->sendJson($jsonView->render());
    }

   

    public function buildBody($path) {
        $viewPath = $this->module->getViewPath() . '/' . $path;
        if (DIRECTORY_SEPARATOR != "/") {
            $viewPath = preg_replace("/\//", DIRECTORY_SEPARATOR, $viewPath);
        }
        $content = $this->nzView->getContent($viewPath);

        $this->body = $this->nzView->renderContent($content);
    }

    public function buildBodyFromString($string) {
        $this->body = $this->nzView->renderContent($string);
    }

    public function setBody($content, $isAjax = FALSE) {
        if ($isAjax) {
            $this->body = $content;
        }

        $this->body = $content;

        //$this->body = $this->nzView->renderContent($content);
    }

    public function getBody() {
        return $this->body;
    }

    /**
     * 
     * @param string(json encoded) $json
     * @return string
     */
    public function sendJson($json) {
        header('Content-Type: application/json', TRUE);
        return $this->body = $json;
    }

    public function renderContent($content, $isAjax = FALSE) {
        if ($isAjax) {
            return $content;
        }

        return $this->nzView->renderContent($content);
    }

    public function setStatusCode($code) {
        http_response_code($code);
    }

    public function setContentType($type) {
        header('Content-Type: ' . $type);
    }

    public function buildURL($slug, $model) {
        $url = $this->module->getURL($slug);
        if (!strstr($url, '{')) {
            return $url;
        }

        $className = get_class($model);
        $classArray = explode('\\', $className);
        $objectName = end($classArray);

        foreach ($model->toArray() as $k => $v) {
            $replacement = '{' . $objectName . '.' . $k . '}';
            if (strstr($url, $replacement)) {
                return str_replace($url, $replacement, $v);
            }
        }
        return $url;
    }

    public function setViewVar($k, $v) {
        $this->nzView->$k = $v;
    }

    /*
      public function __toString() {

      }
     */
}

