<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Module
 *
 * @author fon-pah
 */
namespace Ospari;
class Module {
     public function init ( \NZ\ControllerContainer $container) {
        $conf = $container->getNZ_Config();
        
        
    }
    
    public function getAutoloaderConfig(){
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
    
    public function getRoutes() {
        return array(
            '/' => array( '\Ospari\Controller\PostController',  'indexAction' ),
            '/page/{page}' => array( '\Ospari\Controller\PostController',  'indexAction' ),
            '/post/{slug}' => array( '\Ospari\Controller\PostController',  'viewAction' ),
             '/preview' => array( '\Ospari\Controller\PostController',  'viewAction' ),
            '/assets/css/{css_file}.css' => array( '\Ospari\Controller\AssetController',  'cssAction' ),
            '/assets/js/{css_file}.js' => array( '\Ospari\Controller\AssetController',  'javaScriptAction' ),
      
        );
    }
    
    public function getConfig() {
        return include __DIR__ . '/config/module.config.php';
    }

    //for nz2
    public function getClassMap() {
        return __DIR__ . '/autoload_classmap.php';
    }

    //for nz2
    public function getViewPath() {
       return __DIR__ . '/src/'.__NAMESPACE__.'/View';
    }
    
    
    
}


