<?php

$themePath = '';

$engine = new \Handlebars\Handlebars();


$defaultContent = $this->defaultContent;
$indexContent = $this->indexContent;

$default = $engine->render( $defaultContent , array() );

$index = $engine->render($themePath.'/index.hbs', array(
   
    
) );

echo str_replace($default, '{{{body}}}', $index);

