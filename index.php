<?php
// uncomment the following two lines, if you don't use composer
error_reporting(-1);
ini_set('display_errors', 'On');
date_default_timezone_set('Europe/Berlin');

require 'lib/Handlebars/Autoloader.php';
Handlebars\Autoloader::register();

use Handlebars\Handlebars;

$engine = new Handlebars;


      
$engine->addHelper('date_format', function($template, \Handlebars\Context $context, $args, $source) {
   return $context->get('date')->format('Y-m-d\TH:i:s\Z');
    return '11.11.11';
});


$str = '"Planets:<br />{{#each planets}}<h6>{{this}}</h6>{{/each}} {{date format=\'\'}}",';
$str = preg_replace("/\{\{date+(\S)+format='(.*?)\''/", "", $str);
echo $str;
*
+


    echo $engine->render(
    $str,
    array(
        'planets' => array(
            "Mercury",
            "Venus",
            "Earth",
            "Mars"
        ),
        'date' => new \DateTime()
    )
);