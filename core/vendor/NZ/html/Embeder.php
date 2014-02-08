<?php

namespace NZ;

Class Embeder {

    protected $url;
    protected $height;
    protected $width;

    public function __construct($url, $width = 640, $height = 360) {
        $this->url = $url;
        $this->height = $height;
        $this->width = $width;
    }

    public function toHtml() {
        $url = $this->url;

        if ($this->getType($url) == 'youtube') {
            $nzUri = new Uri($url);

            if (!$v = $nzUri->get('v')) {
                return null;
            }
            return '<iframe width="'.$this->width.'" height="'.$this->height.'" src="//www.youtube-nocookie.com/embed/'.$v.'" frameborder="0" allowfullscreen></iframe>';
            
        }
    }

    public function getType($url) {
        //$url = "http://de.youtube.com/watch?v=a_PKdjXDeiw&feature=dir";
        $pattern = "/^(http|https):\/\/((.*?)\.youtube)(\.de|.com)(:(\d+))?\/watch\?v=([-A-Z0-9_]+)(.*?)/i";

        $r = preg_match($pattern, $url);
        if ($r) {
            return 'youtube';
        }
    }

}
