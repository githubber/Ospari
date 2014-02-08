<?php


require (__DIR__ . '/core/config/constants.php' );


define('OSPARI_PATH', __DIR__);
define('NZ_SESSION_TABLE', OSPARI_DB_PREFIX.'sessions');
define('NZ_MODULE_PATH', __DIR__ . '/core/modules');
define('NZ2_PATH', __DIR__ . '/core/vendor/NZ');
define('Z2_PATH', __DIR__ . '/core/vendor/Zend');

require_once NZ2_PATH . '/app/ClassLoader.php';

$loader = \NZ\ClassLoader::getInstance();
$loader->registerAutoloadMap(NZ2_PATH . '/autoload_classmap.php');
$loader->registerAutoloadMap(Z2_PATH . '/autoload_classmap.php');
$loader->register();

require __DIR__ . '/core/vendor/Handlebars/Autoloader.php';
Handlebars\Autoloader::register();
require( __DIR__.'/core/Bootstrap.php' );

$bs = \Ospari\Bootstrap::getInstance();
define('OSPARI_URL', $bs->detectOspariURL() );


$ret = include(__DIR__ . '/core/config/application.config.php');

\NZ\Config::setArray($ret);
$appConfig = \NZ\Config::getInstance();


$app = new \NZ\Application($appConfig);

$app->getRouter()->before(function( $route ) {
    $bs = \Ospari\Bootstrap::getInstance();
    $bs->checkUserPerms($route);
    
});

$app->getRouter()->on404( array( 'OspariAdmin\Controller\BaseController', 'onPageNotFound' ) ); 

$bs = \Ospari\Bootstrap::getInstance();

try {
    
    $uri = $bs->getRequestURI();
    $app->run($uri);
    
} catch (\Exception $exc) {
    $bs->handleExecption($exc);
    
}

exit(1);
