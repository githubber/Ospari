<?php

if( ENV == 'dev' ){
    $db_name = 'ospari2';
    error_reporting(-1);
    ini_set("display_errors", "on");
    $host = '127.0.0.1';
    
}else {
    $db_name = 'ospari2';        
    $host = '127.0.0.1';
}

/** 
 * Enter database name, username and password
 */
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
