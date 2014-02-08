<?php

namespace NZ;

Class LinkCrawler {
    private $doc;
    private $lang;
    private $url;

    public function __construct(\Lang $lang)
    {
        $this->lang = $lang;
    }
    
    
    private function getContent($url)
    {
        try 
        {
            $content = file_get_contents($url);
        } catch (\Exception $exc) {
            throw new \Exception('Unreachable URL ('.$url.')');
        }

        if( !$content )
        {
            throw new \Exception('Unreachable URL ('.$url.')');
        }
        return $content;
    }
    
    
    public function check( $url, $targetUrl,$anchorText )
    {
        
        $lang = $this->lang;
        if(!$url)
        {
            throw new \Exception($lang->invalid_url);
        }
        if(!$targetUrl)
        {
            throw new \Exception($lang->invalid_target_url);
        }
        if(!$anchorText)
        {
            throw new \Exception($lang->invalid_anchor_text);
        }
        $this->url= $url;
        $doc = $this->getDoc($url);
        $hrefNodes = $doc->getElementsByTagName('a');
        $res = false;
        foreach( $hrefNodes as $node ){
            if( $this->isSameURL($node, $targetUrl)  ){
                if( $this->isSameText($node, $anchorText) ){
                    $res = true;
                    break;
                }
                
            }
        }
        return $res;
        
    }
    
    /**
     * 
     * @param type $url
     * @return \DOMDocument
     */
    private function getDoc( $url ){
        
        if( $doc = $this->doc ){
            return $doc;
        }
        
        $cont = $this->getContent($url);
        $doc = @\DOMDocument::loadHTML($cont);
        $this->doc = $doc;
        return $doc;
    }


    private function isAnchorImage( $node, $anchorText){
        $html = $this->getDoc($this->url)->saveXML( $node ); 
        
        if(preg_match("/<img/", $html) ){
            return TRUE;
        }
        
        return FALSE;
        
    }


    private function  isSameText($node,  $anchorText ){
        $nodeValue = $node->nodeValue;
        if( $nodeValue == $anchorText ){
            return TRUE;
        }
        
        $nodeValue = $this->cleanAnchorText($nodeValue);
        $anchorText = $this->cleanAnchorText($anchorText);
        
        if(preg_match("/image/", $anchorText) ){
            return $this->isAnchorImage( $node, $anchorText);
        }

       if( $nodeValue == $anchorText ){
            return TRUE;
        }
        
        return FALSE;
    }
    
    
    private function  isSameURL($node,  $targetUrl ){
        $url = rtrim($node->getAttribute('href'),'/');
        return ( $url == rtrim($targetUrl,'/') );
    }

   
    public function cleanAnchorText($text) {

        $text = str_replace('&#8230;', '...', $text);
        $text = str_replace('&#8211;', '-', $text);
        $text = str_replace("&#8212;", "--", $text);
        $text = str_replace('&#8243;', '"', $text);
        $text = str_replace('&#8221;', '"', $text);
        $text = str_replace('&#8220;', '"', $text);

        $text = str_replace('&#252;', 'Ã¼', $text);
        $text = str_replace('&#228;', 'Ã¤', $text);
        $text = str_replace("&#038;", "&", $text);

        $text = str_replace("â€™", "'", $text);
        $text = str_replace("&nbsp;", " ", $text);
        $text = str_replace("â€“", "--", $text);
        $text = str_replace("’", "'", $text);
        



        $text = str_replace('<b>', '', $text);
        $text = str_replace('</b>', '', $text);

        $text = str_replace('<i>', '', $text);
        $text = str_replace('</i>', '', $text);

        $text = str_replace('<strong>', '', $text);
        $text = str_replace('</strong>', '', $text);

        $text = str_replace('<br>', ' ', $text);
        $text = str_replace('<br />', ' ', $text);

        $text = str_replace("\r", " ", $text);

        $text = str_replace("\n", " ", $text);
        $text = str_replace(chr(173), " ", $text);
        $text = str_replace(chr(0xC2) . chr(0xA0), " ", $text);

        $text = str_replace('  ', ' ', $text);
        $text = str_replace('  ', ' ', $text);
        $text = str_replace('  ', ' ', $text);

        $text = implode(' ', preg_split("/\s+/", $text));


        $text = trim($text);


        return $text;
    }

//    public function __construct($url) {
//     
//        
//        
//    }
//
//    
//    
//    
//    
//    private function buildDom($content) {
//        $doc = new DOMDocument();
//        $doc->loadHTML($content);
//        return $doc;
//    }
//
//
//    private function fetchContent() {
//
//        if ($this->content) {
//            return $this->content;
//        }
//
//
//        require_once 'Zend/Http/Client.php';
//        $client = new Zend_Http_Client($this->url);
//        $response = $client->request();
//        $body = $response->getBody();
//        $this->content = $body;
//        return $body;
//    }

}