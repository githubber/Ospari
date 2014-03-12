<?php

// enter your custom 
date_default_timezone_set('Europe/Berlin');

/**
 * please define your own
 */
define('OSPARI_SALT', md5( __DIR__.''.$_SERVER['SERVER_ADDR'] ) );

/**
 * You can define your owen admin URL for Ospari
 */
define('OSPARI_ADMIN_PATH', 'admin');

/**
 * Prefix for (my)sql tables
 */
define('OSPARI_DB_PREFIX', 'op_');

/*
 * For Sqlite no prefix is needed
 */
//define('OSPARI_DB_PREFIX', '');


if( getenv('APPLICATION_ENV') == 'local'){
    define('COOKIE_DOMAIN', '.ospari.loc');
    define('ENV', 'dev'); 
}else{  
    define('ENV', 'prod'); 
}

if( ENV == 'dev' ){
    error_reporting(-1);
    ini_set('display_errors', 'On');
}