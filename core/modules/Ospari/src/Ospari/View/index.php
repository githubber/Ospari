<?php

$themePath = '';

$engine = new \Handlebars\Handlebars();


$defaultContent = $this->defaultContent;
$postContent = $this->indexContent;

$pagination = $this->pagination;

$paginationData = array(
    'next' => $pagination->getNext(),
    'prev' =>  $pagination->getPrevious(),
);


$paginationResult = $engine->render($this->paginationContent, $paginationData);


$postContent = str_replace('{{pagination}}', $paginationResult, $postContent);



$postContent = preg_replace_callback("/\{\{([a-z0-9_]+)\s+(.*?)\}\}/ms", function($r) {

    $ra = explode(' ', $r[0]);
    return $ra[0] . '}}';
}, $postContent);

$defaultContent = str_replace('{{{body}}}', $postContent, $defaultContent);

$blog = $this->blog;
$posts = $this->posts;
$setting = $this->setting;

$data = array(
    'meta_title' => $blog->title,
    'posts' => $posts,
);

foreach (get_object_vars($blog) as $k => $v) {
    $data['blog_' . $k] = $v;
}

$engine->setEscape( function( $r ){return $r;} );
$default = $engine->render($defaultContent, $data);

echo $default;
