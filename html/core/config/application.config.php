<?php

if( getenv('APPLICATION_ENV') == 'development' || getenv('APPLICATION_ENV') == 'local' ){
    $db_name = 'ospari_org_dev';
    error_reporting(-1);
    ini_set("display_errors", "on");
    $host = '127.0.0.1';
    
}else {
    $db_name = 'ospari_org';        
    $host = 'localhost';
}


$db = array(
    'database' => $db_name,
    'username' => 'root',
    'password' => 'root',
    'host' => $host,
    'options' =>  array(
                    'buffer_results' => true,
                    ),
    
);

return array(
    'db_read' => $db,
    'db_write' => $db,
    'modules' => array( 'Ospari', 'OspariAdmin'  ),
);
