<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of NZ_Router
 *
 * @author 28h
 */

namespace NZ;

class Router {

    private $simpleRoutes = array();
    private $complexRoutes = array();
    private $errorCodes = array();
    protected $beforeCallback;
    protected $response;

    /**
     *
     * @var \NZ\ModuleContainer
     */
    protected $moduleContainer;

    public function __construct() {
        $this->on404(array($this, 'printError'));
        $this->response = new HttpResponse();
    }

    public function on404($callBack) {
        $this->errorCodes[404] = $callBack;
    }

    public function setModuleContainer(ModuleContainer $mContainer) {
        $this->moduleContainer = $mContainer;
    }

    public function setModuleForController($route, $callback) {
        if (!$this->moduleContainer) {

            return;
        }

        if ($module = $this->moduleContainer->getModule($route)) {
            $this->response->setModule($module);
        }
        
        if (!is_array($callback)) {
            return FALSE;
        }
        
        if ($callback[0] instanceof Controller) {
            if ($module = $this->moduleContainer->getModule($route)) {

                $callback[0]->setModule($module);
                
            }
        }
    }

    public function any($route, $callBack) {
        $this->_add($route, $callBack);
    }

    public function delete($route, $callBack) {
        $this->_add($route, $callBack);
    }

    public function get($route, $callBack) {
        $this->_add($route, $callBack);
    }

    public function post($route, $callBack) {
        $this->_add($route, $callBack, 'POST');
    }

    public function put($route, $callBack) {
        $this->_add($route, $callBack, 'POST');
    }

    private function _add($route, $callBack, $method = NULL) {
        if ($this->isSimple($route)) {
            $obj = new \stdClass();
            $obj->method = $method;
            $obj->callback = $callBack;
            $this->simpleRoutes[$route] = $obj;

            return TRUE;
        }


        $obj = new \stdClass();
        $obj->method = $method;
        //$obj->regex = $regex;
        $obj->callback = $callBack;

        $this->complexRoutes[$route] = $obj;
    }

    private function parseComplexRoute($route) {
        $route = str_replace('.', '\.', $route);
        $regex = preg_replace("/\{(.*?)\}/", "(.*?)", $route);
        $regex = '^' . str_replace('/', '\/', $regex) . '$';
        return $regex;
    }

    
    private function isSimple($rout) {
        return ( strpos($rout, '{') === FALSE );
    }

    public function before($callback) {
        $this->beforeCallback = $callback;
    }

    protected function removeSlash($request_uri) {
        if ($request_uri == '/') {
            return;
        }

        if (substr($request_uri, -1) == '/') {
            $newRequestUri = substr($request_uri, 0, -1);
            if (!headers_sent()) {
                header('location: ' . $newRequestUri, TRUE, 301);
                exit(1);
            } else {
                $newRequestUri = htmlentities($newRequestUri, ENT_QUOTES, "UTF-8");
                ;
                exit('try this URL <a href="' . $newRequestUri . '">' . $newRequestUri . '</a>');
            }
        }
    }

    public function run($request_uri = NULL) {
        if (!$request_uri) {
            $arr = explode('?', $_SERVER['REQUEST_URI']);
            $request_uri = $arr[0];
        }

        $this->removeSlash($request_uri);

        $callback = $this->beforeCallback;
        if ($callback) {
            if (is_array($callback)) {
                $callback = array(new $callback[0], $callback[1]);
            }
            call_user_func_array($callback, array($request_uri));
        }
        
        if (isset($this->simpleRoutes[$request_uri])) {
            $obj = $this->simpleRoutes[$request_uri];
            if ($obj->method) {
                if (!$this->methodMatch($obj)) {
                    return $this->printExeption(new \Exception('Bad Request'));
                }
            }

            $callback = $obj->callback;
            
            if (is_array($callback)) {
                $callback = array(new $obj->callback[0](HttpRequest::getInstance(), $this->response), $callback[1]);
            }

            $this->setModuleForController($request_uri, $callback);
            $this->writeLog($obj->callback);
            
            
            if ($this->isControllerSubClass($callback)) {                
                echo call_user_func($callback);
            } else {                
                call_user_func_array($callback, array(HttpRequest::getInstance(), $this->response));
                echo $this->response->getBody();
            }

            return TRUE;
        }

        $complexRoutes = $this->complexRoutes;
        krsort($complexRoutes);

        foreach ($complexRoutes as $k => $obj) {
            //$pattern = "/" . $obj->regex . "/";
            $pattern = "/" . $this->parseComplexRoute($k) . "/";

            $matches = array();
            if (preg_match($pattern, $request_uri, $matches)) {

                $this->buildRouteParams($k, $pattern, $matches);

                if ($obj->method) {
                    if (!$this->methodMatch($obj)) {
                        return $this->printExeption(new \Exception('Bad Request'));
                    }
                }

                unset($matches[0]);
                $callback = $obj->callback;
                if (is_array($callback)) {
                    $callback = array(new $obj->callback[0](HttpRequest::getInstance(), $this->response), $callback[1]);
                }
                $this->setModuleForController($k, $callback);
                //echo call_user_func_array($callback, $matches);

                if ($this->isControllerSubClass($callback)) {
                    echo call_user_func_array($callback, $matches);
                } else {                    
                    call_user_func_array($callback, array(HttpRequest::getInstance(), $this->response));
                    echo $this->response->getBody();
                }


                return TRUE;
            }
        }

        //$this->errorCodes[404]




        $callback = $this->errorCodes[404];
        if (is_array($callback)) {
            $callback = array(new $callback[0](HttpRequest::getInstance(), $this->response), $callback[1]);
        }

        
        if ('\NZ\Conrtoller' == get_parent_class($callback)) {
            //echo call_user_func_array($callback, $matches);
            echo call_user_func_array($callback, array(404));
        } else {
            call_user_func_array($callback, array(HttpRequest::getInstance(), $this->response ));
            echo $this->response->getBody();
        }
        
         //echo call_user_func_array($callback, array(404));
        
    }

    private function isControllerSubClass($callback){        
        if(!is_array($callback)){
            return false;
        }        
        
        $parentClass = get_parent_class($callback[0]);
        
        if('NZ_Controller' == $parentClass ){
            return true;
        }
        
        return ('NZ\Controller' ==  $parentClass );
    }
    
    private function buildRouteParams($route, $pattern, $matches) {
        $routKeys = array();
        preg_match($pattern, $route, $routKeys);
        //print_r( $routVars);
        //print_r($matches);
        //exit(1);
        $routerVals = $matches;
        unset($routerVals[0]);
        unset($routKeys[0]);

        foreach ($routKeys as $k => $v) {
            $v = str_replace('{', '', $v);
            $v = str_replace('}', '', $v);
            $routKeys[$k] = $v;
        }

        $routerParams = array_combine($routKeys, $routerVals);

        $req = HttpRequest::getInstance();
        $req->setRouteParams($routerParams);

        return $routerParams;
    }

    public function methodMatch($obj) {
        if (!isset($_SERVER['REQUEST_METHOD'])) {
            return FALSE;
        }

        return ( $_SERVER['REQUEST_METHOD'] == $obj->method );
    }

    private function printExeption(\Exception $e) {
        echo $e->getMessage();
    }

    private function printError($code) {
        //echo $e->getMessage();
        
        
        if ($code == 404) {
            echo 'Page not found.';
        }
    }

    protected function writeLog($str) {
        //print_r( $str );
        //echo "<br>";
    }

}