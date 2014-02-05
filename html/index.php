<?php
error_reporting(-1);
ini_set('display_errors', 'On');
date_default_timezone_set('Europe/Berlin');
define('NZ_MODULE_PATH', __DIR__ . '/core/modules');
define('OSPARI_SALT', '2cf24dba5fb0a30e26e83b2ac5b9e29e1b161e5c1fa7425e73043362938b9828');
define('OSPARI_PATH', __DIR__);
define('OSPARI_ADMIN_PATH', 'admin');
define('OSPARI_DB_PREFIX', 'op_');

if( getenv('APPLICATION_ENV') == 'local'){
    define('SITE_NAME', 'Blog Ospari-local');
    define('OSPARI_DOMAIN', 'blog.ospari.loc:8888');
    define('OSPARI_URL', 'http://blog.ospari.loc:8888');
    define('COOKIE_DOMAIN', '.blog.ospari.loc');
    define('ENV', 'dev'); 
}elseif(getenv('APPLICATION_ENV') == 'development'){
    define('SITE_NAME', 'Blog Ospari-Dev');
    define('OSPARI_DOMAIN', 'ospari.blog.rankstat.co');
    define('OSPARI_URL', 'http://ospari.blog.rankstat.co');
    define('ENV', 'dev'); 
}else{  
    define('OSPARI_URL', 'http://blog.ospari.org');
    define('SITE_NAME', 'Blog Ospari');
   
}
define('NZ2_PATH', __DIR__ . '/core/vendor/NZ');
define('Z2_PATH', __DIR__ . '/core/vendor/Zend');

require_once NZ2_PATH . '/app/ClassLoader.php';

$loader = \NZ\ClassLoader::getInstance();
$loader->registerAutoloadMap(NZ2_PATH . '/autoload_classmap.php');
$loader->registerAutoloadMap(Z2_PATH . '/autoload_classmap.php');
$loader->register();

require __DIR__ . '/core/vendor/Handlebars/Autoloader.php';
Handlebars\Autoloader::register();

$ret = include(__DIR__ . '/core/config/application.config.php');

\NZ\Config::setArray($ret);
$appConfig = \NZ\Config::getInstance();

$session = \NZ\SessionHandler::getInstance();

$app = new \NZ\Application($appConfig);

$app->getRouter()->before(function( $route ) {
    
    $arr = explode('/', $route);
    $endEl = end($arr);
    $allowedPaths = array(
        'login' => TRUE,
        'reset'=>TRUE,
        'forgotten'=>TRUE
    );
    if( isset( $allowedPaths[$endEl] ) ){
        return TRUE;
    }
    
    $adminPathLength = strlen(OSPARI_ADMIN_PATH)+1;
    if (substr($route, 0, $adminPathLength) == '/'.OSPARI_ADMIN_PATH ) {
        
        $sess = \NZ\SessionHandler::getInstance();
        if (!$sess->getUser_id()) {
            header('location: /'.OSPARI_ADMIN_PATH.'/login?callback=' . urlencode(\NZ\Uri::getCurrent()));
            exit(1);
        }
        $sess->user_id = $sess->getUser_id();
    }
});

$app->getRouter()->on404( array( 'OspariAdmin\Controller\BaseController', 'onPageNotFound' ) ); 



try {

    $uri = get_request_uri();
    

    $app->run($uri);
} catch (\Exception $exc) {
    \http_response_code(500);
    echo '</h1>' . $exc->getMessage() . '</h1>';
    echo '<hr><pre>';
    $debug = $exc->__toString();
    $debug = str_replace($_SERVER['DOCUMENT_ROOT'], '', $debug);
    echo $debug;
    echo '</pre>';
}

function get_request_uri() {
    if (!isset($_SERVER['REQUEST_URI'])) {
        throw new \Exception("\$_SERVER['REQUEST_URI'] not set");
    }

    $cwd = __DIR__;
    $dcPath = str_replace($_SERVER['DOCUMENT_ROOT'], '', $cwd);

    $uri_arr = explode('?', $_SERVER['REQUEST_URI']);

    $uri = str_replace($dcPath, '', $uri_arr[0]);
    return $uri;
}

exit(1);
