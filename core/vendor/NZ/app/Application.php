<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of App
 *
 * @author 28h
 */

namespace NZ;

class Application {
    //put your code here

    /**
     *
     * @var \NZ\Router 
     */
    protected $router;
    protected $moduleContainer;
    protected $conf;

    public function __construct(Config $conf) {
        $this->conf = $conf;
        $this->bootsrap();
    }

    protected function bootsrap() {
        $this->router = new Router();
        $this->moduleContainer = new ModuleContainer();
        $this->lodModules();
    }

    protected function lodModules() {
        if(defined('NZ_MODULE_PATH') ){
            $path = NZ_MODULE_PATH;
        }else{
            $path = $_SERVER['DOCUMENT_ROOT'] . '/../module';
        }
        foreach ($this->conf->get('modules') as $moduleName) {
            require_once ( $path . '/' . $moduleName . '/Module.php' );
            $c = $moduleName . '\Module';
            $module = new $c($this->conf);

            $loader = ClassLoader::getInstance();
            $loader->registerAutoloadMap($module->getClassMap());
            $loader->register();
            $this->processRoutes($module);
            
            //load submodules
            if (is_callable(array($module, 'getSubModules'))) {
                $method = new \ReflectionMethod( $module , 'init');
                if( !$method->isFinal() ){
                    throw new \Exception( "{$moduleName}::init must be declared as final. like: <code>final public function init</code>" );
                }
                
                
                
                foreach ($module->getSubmodules() as $subName) {
                    require_once ( $path . '/' . $moduleName . '/submodules/' . $subName . '/Module.php' );
                    if (!is_subclass_of($subName.'\Module', $moduleName.'\Module')) {
                        echo "$subName.'\Module', $moduleName.'\Module'<br>";
                        throw new \Exception("{$subName}\Module is not subclass of " . get_class($module));
                    }

                    

                    $c = $subName . '\Module';
                    $module = new $c($this->conf);

                    $loader = new \Zend\Loader\ClassMapAutoloader();
                    $loader->registerAutoloadMap($module->getClassMap());
                    $loader->register();
                    $this->processRoutes($module);
                }
            }
        }
    }

    public function addModule($moduleName, $path) {
        require_once( $path . '/' . $moduleName . '/Module.php' );
        $c = $moduleName . '\Module';
        $module = new $c($this->conf);

        $loader = new \Zend\Loader\ClassMapAutoloader();
        $loader->registerAutoloadMap($module->getClassMap());
        $loader->register();
        $this->processRoutes($module);
    }

    protected function processRoutes($module) {
        //$this->moduleContainer = new ModuleContainer();

        $mContainer = $this->moduleContainer;
        foreach ($module->getRoutes() as $route => $callback) {
            if ($_Module = $mContainer->getModule($route)) {
                throw new \Exception('Route conflict. ' . $route . ' defined in ('.get_class($module).') exists in Module ' . get_class($_Module));
            }
            $mContainer->add($route, $module);
            $this->router->any($route, $callback);
        }
        $this->mContainer = $mContainer;

        //$this->router = $router;
    }

    public function getRouter() {
        return $this->router;
    }

    public function run($request_uri = NULL) {
        $this->router->setModuleContainer($this->mContainer);
        $this->router->run($request_uri);
    }

}

