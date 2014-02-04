<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Controller
 *
 * @author 28h
 */

namespace NZ;

use \Zend\Http;
use \Zend\View\View as ZendView;

class Controller {

    /**
     *
     * @var \Zend\Http\Request
     */
    protected $req;

    /**
     *
     * @var \View 
     */
    protected $view;

    /**
     *
     * @var type 
     */
    protected $nzView;

    /**
     * current User
     * @var \User 
     */
    protected $user;

    /**
     * current session object
     * @var SessionHandler; 
     */
    protected $session;
    protected $conf;
    protected $module;

    public function __construct() {
        $this->init();
    }

    protected function getContainer() {
       
        $this->init();
        $cont = new \NZ\ControllerContainer();
        $cont->setNZ_Config($this->conf);
       
        
        $cont->setSession($this->session);
        return $cont;
    }

    public function init() {
        $this->setRequest();

        $conf = \NZ\Config::getInstance();
        $this->conf = $conf;

        $view = \NZ\View::getInstance();
        $view->head = $conf->headTPL;
        $view->tail = $conf->tailTPL;


        $this->nzView = $view;

        $sess = SessionHandler::getInstance();
        
        $this->session = $sess;
    }

    private function setRequest() {
        $req = new Http\Request();
        $req->setPost(new \Zend\Stdlib\Parameters($_POST));
        //$req->setG( new \Zend\Stdlib\Parameters( $_POST ) );
        $req->setQuery(new \Zend\Stdlib\Parameters($_GET));
        $this->req = $req;
    }

    /**
     * 
     * @return \Zend\Http\Request
     */
    public function getRequest() {
        return $this->req;
    }

    /**
     * 
     * @return \NZ\HttpRequest
     */
    public function getNZ_Request() {
        return HttpRequest::getInstance();
    }

    public function getNZ_Uri() {
        return new \NZ\Uri();
    }

    /**
     * 
     * @return \NZ\Config
     */
    public function getNZ_Config() {
        return $this->conf;
    }

    /**
     * 
     * @return \Zend\View\View
     */
    public function getView() {
        return $this->view;
    }

    /**
     * 
     * @return \NZ\View
     */
    public function getNZ_View() {
        return $this->nzView;
    }

    /**
     * 
     * @return \NZ\SessionHandler
     */
    public function getSession() {
        return $this->session;
    }

    public function renderViewModel($model) {
        $resolver = $this->getTemplateResolver();

        return $this->renderViewModel_PHP($resolver, $model);
    }

    public function renderViewModel_PHP($resolver, $model) {
        $renderer = new \Zend\View\Renderer\PhpRenderer();

        $renderer->setResolver($resolver);

        return $renderer->render($model);
    }

    public function renderContent($content, $isAjax = FALSE) {
        if ($isAjax) {
            return $content;
        }

        return $this->nzView->renderContent($content);
    }

    public function render404($content = 'Page not found') {
        NZ_HttpResponse::notFound();
        return $this->view->renderContent($content);
    }

    public function redirect($location, $code = 302) {
        if (headers_sent()) {
            return '<a href="' . $location . '">' . $location . '</a>';
        }

        header('location: ' . $location, TRUE, $code);
        exit(1);
    }

    public function getTemplateResolver() {
        throw new Exception(__METHOD__ . ' must be implemented by your controller.');
    }

    public function renderException(\Exception $e) {
        http_response_code(500);
        return '<h1>NZ: Exception </h1><pre>' . print_r($e, TRUE) . '<pre>';

        if (getenv('APPLICATION_ENV') == 'development') {
            return '<h1>NZ: Exception </h1><pre>' . print_r($e, TRUE) . '<pre>';
        }

        return $this->renderContent( $e->getMessage() );
    }

    public function setModule($module) {
        $module->init($this->getContainer());
        $this->module = $module;
        $this->init();
    }

    public function getModule() {
        return $this->module;
    }

}

