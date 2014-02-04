<?php

$themePath = '';

$engine = new \Handlebars\Handlebars();

$defaultContent = $this->defaultContent;
$postContent = $this->postContent;

$postContent = str_replace('foreach', 'each', $postContent);


$defaultContent = str_replace('{{{body}}}', $postContent, $defaultContent);

$engine->addHelper('asset', function($template, \Handlebars\Context $context, $args, $source) {
   return OSPARI_URL.'/assets/'.  str_replace('"', '', $args);

});


$blog = $this->blog;
$post = $this->post;

$data = array(
    'meta_title' => $post->title,
    'post' => $post,
);

foreach (get_object_vars($blog) as $k => $v) {
    $data['blog_' . $k] = $v;
}

$engine->setEscape( function( $r ){return $r;} );
$default = $engine->render($defaultContent, $data);

echo $default;
