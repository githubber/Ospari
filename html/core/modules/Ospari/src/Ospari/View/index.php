<?php

$themePath = '';

$engine = new \Handlebars\Handlebars();


$defaultContent = $this->defaultContent;
$postContent = $this->indexContent;

$postContent = str_replace('foreach', 'each', $postContent);
$postContent = preg_replace_callback("/\{\{([a-z0-9_]+)\s+(.*?)\}\}/ms", function($r) {

    $ra = explode(' ', $r[0]);
    return $ra[0] . '}}';
}, $postContent);

$defaultContent = str_replace('{{{body}}}', $postContent, $defaultContent);


$ret = array();
for ($i = 0; $i < 4; $i++) {
    $post = new stdClass;
    $post->title = 'title ' . $i;
    $post->content = str_repeat('content ' . $i . ' ', 200);
    $post->post_class = 'post-class_' . $i;
    $ret[] = get_object_vars($post);
}

$blog = $this->blog;
$posts = $this->posts;

$data = array(
    'meta_title' => 'home',
    'posts' => $posts,
);

foreach (get_object_vars($blog) as $k => $v) {
    $data['blog_' . $k] = $v;
}

$engine->setEscape( function( $r ){return $r;} );
$default = $engine->render($defaultContent, $data);

echo $default;
