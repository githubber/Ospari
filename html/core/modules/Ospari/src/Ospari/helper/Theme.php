<?php

namespace Ospari\Helper;

Class Theme {

    protected $option;
    protected $defaultContent;
    protected $indexContent;
    protected $postContent;

    public function __construct() {
        
    }

    public function getDefaultContent() {
        return $this->defaultContent;
    }

    public function getIndexContent() {
        return $this->indexContent;
    }

    public function getPostContent() {
        return $this->postContent;
    }

    public function getPath() {
        $setting = \OspariAdmin\Model\Setting::getAsStdObject();
        if(!isset( $setting->theme)){
            $theme = 'simply-pure';
        }
        
        $themePath = OSPARI_PATH . '/content/themes/'.$theme;
        
        return $themePath;
    }

    public function prepare() {
        $themePath = $this->getPath();

        $defaultContent = file_get_contents($themePath . '/default.hbs');
        $indexContent = file_get_contents($themePath . '/index.hbs');

        $postContent = file_get_contents($themePath . '/post.hbs');

        $this->defaultContent = $this->replaceGlobals($defaultContent);
        $this->indexContent = $this->replaceGlobals($indexContent);
        $this->postContent = $this->replaceGlobals($postContent);
    }

    private function replaceGlobals($content) {
        /**
         * replace @blog.something with blog_something
         */
        $content = preg_replace("/@blog\.(.*?)/", "blog_$1", $content);
        $content = str_replace("{{asset", "{{#asset", $content);

        /** replaces alle {{var arg="val"}} with just var 
         * 
         */
        $content = preg_replace_callback("/\{\{([a-z0-9_]+)\s+(.*?)\}\}/ms", function($r) {

            $ra = explode(' ', $r[0]);
            return $ra[0] . '}}';
        }, $content);


        return $content;
    }

}
