<?php

namespace NZ;
Class HtmlParser {

    private $url;
    protected $scheme = 'http://';
    private $folder;
    private $host;
    private $doc;
    public $urlInfo = array();
    protected $links = array();
    protected $htmlContent;
    protected $parsedURLs = array();

    protected $subDomains = array();
    protected $external = array();


    public function __construct($url, $htmlContent ) {
        $pUrl = parse_url($url);
        $this->folder = dirname($url);
        $this->host = $pUrl['host'];
        $this->url = $url;
        if( isset(  $pUrl['scheme'] ) ){
            $this->scheme = $pUrl['scheme'].'://';
        }
        $this->htmlContent = $htmlContent;
         $doc = @\DOMDocument::loadHTML($htmlContent);
        $this->doc = $doc;
    }

    function getTitle() {
        //$t = $this->doc->getElementsByTagName('title');
        $t = '';
        foreach ($this->doc->getElementsByTagName('title') as $node) {
            $t = $node->nodeValue;
        }
        return $t;
    }
    
    function getDescription() {
        //$t = $this->doc->getElementsByTagName('title');
        $t = '';
        foreach ($this->doc->getElementsByTagName('meta') as $node) {
            if( $node->getAttribute('name') == 'description' ){
                return $node->getAttribute('content');
            }
        }
        return $t;
    }
    
    function getThumb() {
        //$t = $this->doc->getElementsByTagName('title');
        $t = '';
        foreach ($this->doc->getElementsByTagName('title') as $node) {
            $t = $node->nodeValue;
        }
        return $t;
    }
    
    
    /**
     * 
     * @return type
     */
    public function getHrefs(){
       
        $doc = $this->doc;
        $links = array();
        foreach ($doc->getElementsByTagName('a') as $node) {
            $href = $node->getAttribute('href');
            if ($f = $this->buildFilePath($href)) {
               $node->setAttribute('href', $f);
               $links[$f] = $node;
            }
        }
        return $links;
    }

    public function nodeToHTML( \DOMElement $e ){
        return $this->doc->saveXML( $e );
    }

        protected function fetchLinks($str, $ext = null) {
        $doc = @\DOMDocument::loadHTML($str);
        $this->doc = $doc;
        $links = array();
        foreach ($doc->getElementsByTagName('a') as $node) {
            $href = $node->getAttribute('href');
            if ($f = $this->buildFilePath($href, $ext)) {
                $title = strip_tags($node->nodeValue);
                if ($title) {
                    $title = str_ireplace('.' . $ext, '', $title);
                } else {
                    $title = $this->pathinfo_im($href);
                }
                $links[$f] = $title;
            }
        }
        return $links;
    }

    protected function getImages($str, $ext = null) {
        $doc = @DOMDocument::loadHTML($str);
        $this->doc = $doc;
        $links = array();
        foreach ($doc->getElementsByTagName('img') as $node) {
            $src = $node->getAttribute('src');

            if ($f = $this->fetchFile($src, $ext)) {
                $title = $node->getAttribute('alt');
                if ($title) {
                    $title = str_ireplace('.' . $ext, '', $title);
                } else {
                    $title = $this->pathinfo_im($href);
                }
                $links[$f] = $title;
            }
        }
        return $links;
    }

    protected function fixUrl($url) {
        if(substr($url, 0, 2) == '//' ){
            $url = '{-.#µ#.-}'.$url;
        }
        
        $url = str_replace('//', '/', $url);
        $url =  str_replace('http:/', 'http://', $url);
        $url = str_replace('{-.#µ#.-}', '/', $url);
        return $url;
        
    }

    protected function buildFilePath($url, $ext = null) {
        if( empty( $url ) ){
            return NULL;
        }
        
        if(substr($url, 0, 1) == '#' ){
            return NULL;
        }
        
         if(substr($url, 0, 11) == 'javascript:' ){
            return NULL;
        }

        $this->urlInfo = $this->urlInfo($url);

        $theURL = null;

        if ($this->isExtern($url)) {
            return $url;
        }

        if ($this->isAbsolute($url)) {
            $theURL = $url;
        } else if (substr($url, 0, 1) == '/') {
            $theURL = $this->scheme . $this->host . '/' . $url;
        } else if (substr($url, 0, 3) == '../') {
            $theURL = $this->scheme . $this->host . '/' . $url;
        } else {
            //print_r($this);
            $theURL = $this->host.'/'.$this->host . '/' . $url;
            
        }

        if ($ext == null) {
          
            return $this->fixUrl($theURL);
        }

        if (!isset($this->urlInfo['extension'])) {

            return null;
        }

        if (strtolower($this->urlInfo['extension']) == strtolower($ext)) {
            return $this->fixUrl($theURL);
        }
    }

    protected function urlInfo($url) {
        $pURL = parse_url($url);

        $p = array();
        if (isset($pURL['path'])) {
            $p = pathinfo($pURL['path']);
        }
        if(!is_array($p)||!isset($pUrl)) {
            return $p;
        }
        return $pURL + $p;
    }

    protected function isAbsolute($url) {
        //return isset($this->urlInfo['host']);
        
        
        if( substr( $url, 0, 2 ) == '//' ){
            return TRUE;
        }
        $pURL = $this->parseURL($url);
        return isset( $pURL['scheme'] );
        
    }

    protected function pathinfo_im($path) {

        $tab = $this->urlInfo;
        $tab["basenameWE"] = substr($tab["basename"], 0, strlen($tab["basename"]) - (strlen($tab["extension"]) + 1));
        return $tab["basenameWE"];
    }

    public function isSubdomain($url) {
        if( empty( $url ) ){
            return FALSE;
        }

        $this->isExtern($url);
        
        if( isset( $this->subDomains[$url]) ){
            return TRUE;
        }
        
        
        return FALSE;
    }
    
    public function isExtern($url) {
        if( empty( $url ) ){
            return FALSE;
        }

        if(  isset( $this->external[$url] ) ){
            return $this->external[$url];
        }
        
         if( isset( $this->subDomains[$url]) ){
            return FALSE;
        }
        
        
         $pUrl = $this->parseURL($url);
        
          if (!isset($pUrl['host'])) {
            return false;
        }
         
        $theHost = $this->host;
        $host = $pUrl['host'];
        
        if( $host == $theHost ){
            return FALSE;
        }
        
        $theHost = str_replace('www.', '', $theHost);
        $pattern = preg_quote($theHost.'/', '/');
        
      
         //echo ("/$pattern/ ###". $host).'<hr>';
        if(preg_match("/$pattern/", $host.'/' ) ){
             
            $this->subDomains[$url] = $url;
            return FALSE;
        }
        
        $this->external[$url] = $url;
        
        /*
        $theLen = strlen( $theHost );
        if( $theHost == substr( $host ,  ($theLen-($theLen*2)) ) ){
            return FALSE;
        }
        */
        
        return TRUE;
    }

    protected function parseURL( $url ){
        
        if( isset( $this->parsedURLs[$url] ) ){
            return $this->parsedURLs[$url];
        }
        
        $pUrl = parse_url($url);
        $this->parsedURLs[$url] = $pUrl;
        return $pUrl;
    }
    
}


