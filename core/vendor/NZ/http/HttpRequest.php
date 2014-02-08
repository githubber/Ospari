<?php

namespace NZ;

Class HttpRequest {

    private $params = array();
    private $queryParams = array();
    private $routeParams = array();
    private static $instance;

    /**
     * 
     * @param array $param
     * @return \NZ\HttpRequest
     */
    public static function getInstance($param = null) {
        if (self::$instance === null) {
            self::$instance = new HttpRequest($param);
        }
        return self::$instance;
    }

    function __construct($param = null) {
        if ($param === null) {
            $this->params = $_REQUEST;
            foreach ($_COOKIE as $k => $v) {
                $this->params[$k] = $v;
            }
        } else {
            $this->params = $param;
        }

        if (isset($_SERVER['HTTP_REFERER'])) {
            $this->params['http_referer'] = $_SERVER['HTTP_REFERER'];
            $this->params['http_refferer'] = $_SERVER['HTTP_REFERER'];
        }
        
        $this->queryParams = $_GET;
        
    }
    
    /**
     *  return the current route
     * @return string
     */
    public function getRoute(){
        if( isset( $_SERVER['REQUEST_URI'] ) ){
         $arr = explode('?', $_SERVER['REQUEST_URI']);
            $request_uri = $arr[0];
            return $request_uri;
        }
        return '';
            
    }

        public function getCurrentUrl(){        
        return Uri::getCurrent();
    }

    public function getSession(){
        return  \NZ\SessionHandler::getInstance();
    }

    public function setRouteParams($params) {
        $this->routeParams = $params;
        foreach ($params as $k => $v) {
            $this->set($k, $v);
        }
    }

    public function removeParam($p) {
        unset($this->params[$p]);
    }

    public function isGet() {
        if (!isset($_SERVER['REQUEST_METHOD'])) {
            return FALSE;
        }

        return ( $_SERVER['REQUEST_METHOD'] == 'GET' );
    }

    public function isPOST() {
        if (!isset($_SERVER['REQUEST_METHOD'])) {
            return FALSE;
        }

        return ( $_SERVER['REQUEST_METHOD'] == 'POST' );
    }

    public function getCookie($k) {
        if (isset($_COOKIE[$k])) {
            return $_COOKIE[$k];
        }
        return NULL;
    }

    public function getQuery($k) {
        if (isset($this->queryParams[$k])) {
            return $this->queryParams[$k];
        }
        return NULL;
    }
    
    public function getQueryParams(){
        return $this->queryParams;
    }
    
    public function setQuery($k, $v){
        $this->queryParams[$k] = $v;
    }

    public function removeQuery($k){
        if (isset($this->queryParams[$k])){
            unset($this->queryParams[$k]);
        }
    }
    
    public function getPost($k) {
        if (isset($_POST[$k])) {
            return $_POST[$k];
        }
        return NULL;
    }
    
    public function setPost($k, $v){
        $_POST[$k] = $v;
        $this->set($k, $v);
    }
    
    public function getPostParams(){
        return $_POST;
    }
    
    public function getRouter($k) {
        if (isset($this->routeParams[$k])) {
            return $this->routeParams[$k];
        }
        return NULL;
    }
    

    public function isAjax() {
        //[HTTP_X_REQUESTED_WITH] => XMLHttpRequest
        if (!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            return FALSE;
        }

        return $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';
    }

    /**
     *
     * @param type $striing parameter name
     * @param type $message string error message for parameter
     */
    public function setErrorMessage($p, $message) {
        $this->set($p . 'err', $message);
    }

    public function setTopErrorMessage($message){
        if(!isset($this->params['topErrorMessage'])){
            $this->setPost('topErrorMessage', $message);
            $this->setPost('topErrorMessageerr', $message);
        } else{
            $this->setPost('topErrorMessage', $message);
            $this->setPost('topErrorMessageerr', $message);
        }
    }
    
    public function getTopErrorMessage(){
        return $this->get('topErrorMessageerr');
    }
    
    public function setTopSuccessMessage($message){
        $this->setPost('topSuccessMessage', $message);
        $this->setPost('topSuccessMessageerr', $message);        
    }
    
    public function getTopSuccessMessage(){
        return $this->get('topSuccessMessageerr');
    }
    
    /**
     *
     * @param type string parameter name
     * @return string error messahe 
     */
    public function getErrorMessage($p) {
        return $this->get($p . 'err');
    }

    public function getAllErrorMessages(){
        $arr = array();
        foreach($this->params as $k => $v){            
            $err = $this->get($k . 'err');
            if($err){
                $arr[$k] = $err;
            }
        }
        return $arr;
    }
    
    public function getValidated($p, $message) {
        if (!$v = $this->get($p)) {
            $this->set($p . 'err', $message);
            return NULL;
        }

        return $v;
    }

    public function hasUpload() {
        if (!$_FILES) {
            return false;
        }

        foreach ($_FILES as $k => $v) {
            if (is_array($v)) {
                if ($v['tmp_name'][0]) {
                    return true;
                }
            } else {
                if ($v['tmp_name']) {
                    return true;
                }
            }
        }
        return false;
    }

    public function refersNotFrom($str) {
        if (!isset($_SERVER['HTTP_REFERER'])) {
            return false;
        }

        if (strpos($_SERVER['HTTP_REFERER'], $str)) {
            return false;
        }
        return true;
    }

    public function isRPC() {
        return isset($GLOBALS["HTTP_RAW_POST_DATA"]);
    }

    public function isParam($p) {
        return isset($this->params[$p]);
    }

    public function toArray() {
        return $this->params;
    }

    public function __get($p) {
        if (isset($this->params[$p])) {
            return $this->params[$p];
        }

        return null;
    }

    public function getTime($p) {
        if (!isset($this->params[$p])) {
            return 0;
        }

        return strtotime($this->params[$p]);
    }

    public function match($k, $arr) {
        if (!isset($this->params[$k])) {
            return null;
        }
        $v = trim($this->params[$k]);
        $keys = array_flip($arr);

        if (isset($keys[$v])) {
            return $v;
        }
        return null;
    }

    public function get($k) {
        if (isset($this->params[$k])) {
            return trim($this->params[$k]);
        }
        return null;
    }
        
    public function getLine($k) {
        if (isset($this->params[$k])) {
            $lines = explode("\n", $this->params[$k]);
            if (isset($lines[1])) {
                return null;
            }
            return $this->params[$k];
        }
        return null;
    }

    public function set($k, $v) {
        $this->params[$k] = $v;
    }

    public function getAllParams(){
        $arr = array();
        foreach($this->params as $k => $v){
            if($this->isParam($k)){
                $arr[] = $this->get($k);
            }            
        }
        return $arr;
    }
    
    public function getArray($k) {
        if (!isset($this->params[$k])) {
            return array();
        }
        return (array) $this->params[$k];
    }

    function getInt($k) {
        if (isset($this->params[$k])) {
            return intval($this->params[$k]);
        } else {
            return 0;
        }
    }

    function getNumeric($k) {
        if (isset($this->params[$k])) {
            if (is_numeric($this->params[$k])) {
                return $this->params[$k];
            }
        }

        return 0;
    }

    public function getPath($k) {
        if (isset($this->params[$k])) {
            $path = preg_replace("/(\.?)/", "", $this->params[$k]);
            return str_replace("//", "/", $path);
        }
        return null;
    }

    function getString($k) {
        if (!isset($this->params[$k])) {
            return NULL;
        }

        return strval($this->params[$k]);
    }
    
    function getAlNum($k) {
        if (!isset($this->params[$k])) {
            return null;
        }


        if (ctype_alnum(trim($this->params[$k]))) {
            return $this->params[$k];
        } else {
            return null;
        }
    }

    function getSqlEscaped($k) {
        if (!isset($this->params[$k])) {
            return null;
        }

        return addslashes($this->params[$k]);
    }

    //more to be done
    function getXSSCleaned($allowedTags = "") {
        return addslashes($this->params[$k]);
    }

    function getEmail($k) {
        if (!isset($this->params[$k])) {
            return null;
        }

        $email = strtolower($this->params[$k]);
        if (!preg_match("/^([_[:alnum:]-]+)(\.[_[:alnum:]-]+)*@([[:alnum:]\.-]+)([[:alnum:]])\.([[:alpha:]]{2,4})$/", $email)) {
            return null;
        } else {
            return $email;
        }
    }
    
    function getFloat($k) {
        if (!isset($this->params[$k])) {
            return floatval(0);
        }
        $va = $this->params[$k];
        $va = str_replace(',', '.', $va);
        return floatval($va);
    }

    function getDouble($k) {
        if (!isset($this->params[$k])) {
            return doubleval(0);
        }
        return doubleval($this->params[$k]);
    }

    function getAz09($k) {
        if (preg_match("/([^0-9a-z])/", strtolower($this->params[$k]))) {
            return null;
        } else {
            return $this->params[$k];
        }
    }

    function getMatched($k, $re) {
        if (!isset($this->params[$k])) {
            return null;
        }
        $str = $this->params[$k];
        if (preg_match($re, $str)) {
            return $str;
        } else {
            return null;
        }
    }

    public function getURL($k) {
        if (!isset($this->params[$k])) {
            return null;
        }
        $url = $this->params[$k];
        if (!preg_match("/^(http|https|ftp):\/\//", $url)) {
            $url = "http://" . $url;
        }

        $check = "/^(http|https|ftp):\/\/(([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+)(:(\d+))?(|\/)/i";
        if (preg_match($check, $url)) {
            return $url;
        } else {

            return null;
        }
    }
        
    public function resolveURI($base, $href) {

        // href="" ==> current url.
        if (!$href) {
            return $base;
        }

        // href="http://..." ==> href isn't relative
        $rel_parsed = parse_url($href);
        if (array_key_exists('scheme', $rel_parsed)) {
            return $href;
        }

        // add an extra character so that, if it ends in a /, we don't lose the last piece.
        $base_parsed = parse_url("$base ");
        // if it's just server.com and no path, then put a / there.
        if (!array_key_exists('path', $base_parsed)) {
            $base_parsed = parse_url("$base/ ");
        }

        // href="/ ==> throw away current path.
        if ($href{0} === "/") {
            $path = $href;
        } else {
            $path = dirname($base_parsed['path']) . "/$href";
        }

        // bla/./bloo ==> bla/bloo
        $path = preg_replace('~/\./~', '/', $path);

        // resolve /../
        // loop through all the parts, popping whenever there's a .., pushing otherwise.
        $parts = array();
        foreach (
        explode('/', preg_replace('~/+~', '/', $path)) as $part
        )
            if ($part === "..") {
                array_pop($parts);
            } elseif ($part) {
                $parts[] = $part;
            }

        return (
                (array_key_exists('scheme', $base_parsed)) ?
                        $base_parsed['scheme'] . '://' . $base_parsed['host'] : ""
                ) . "/" . implode("/", $parts);
    }

    public function getRealUrl($url) {

        foreach (get_headers($url, 1) as $k => $v) {
            if (strtolower($k) == 'location') {
                $f_url = $v;
                if (is_array($v)) {
                    $f_url = end($f_url);
                }
                $f_url = $this->resolveURI($url, $f_url);
                return urldecode($this->getRealUrl($f_url));
            }
        }
        return $url;
    }

    public function getFlash($k) {
        if (!isset($this->params[$k])) {
            return '';
        }

        $doc = new \DOMDocument();
        @$doc->loadHTML(stripcslashes($this->params[$k]));

        $embed = array();
        $object = array();

        foreach ($doc->getElementsByTagName('embed') as $node) {
            foreach ($node->attributes as $k => $v) {
                $embed[$v->name] = $v->value;
            }
            break;
        }

        foreach ($doc->getElementsByTagName('object') as $node) {
            foreach ($node->attributes as $k => $v) {
                $object[$v->name] = $v->value;

                foreach ($doc->getElementsByTagName('param') as $node2) {
                    foreach ($node2->attributes as $k2 => $v2) {
                        $name = $v2->value;
                        if ($name == 'movie') {
                            $object['movie'] = $node2->getAttribute('value');
                        }
                        break;
                    }
                }
            }
            break;
        }



        $ob = '';
        if (isset($object['movie'])) {
            $req = new HttpRequest(array('movie' => $object['movie']));
            if ($src = $req->getURL('movie')) {
                $ob .= '<object ';


                if (isset($object['width'])) {
                    $w = $object['width'];
                    $p = '%';
                    if (strpos($w, $p) != (strlen($w) - 1)) {
                        $p = '';
                    }
                    $ob .= ' width="' . intval($object['width']) . $p . '"';
                }

                if (isset($object['height'])) {
                    $h = $object['height'];
                    $p = '%';
                    if (strpos($h, $p) != (strlen($h) - 1)) {
                        $p = '';
                    }
                    $ob .= ' height="' . intval($object['height']) . '"';
                }

                if (isset($object['style'])) {
                    $ob .= ' style="' . str_replace('javascript', '', $object['style']) . '"';
                }

                $ob .= '><param movie="' . $src . '" />';
            }
        }

        //echo $ob;
        $em = '';
        if (isset($embed['src'])) {

            $req = new HttpRequest(array('src' => $embed['src']));
            if ($src = $req->getURL('src')) {
                $em .= '<embed src="' . $src . '" TYPE="application/x-shockwave-flash" ';

                if (isset($embed['width'])) {
                    $w = $embed['width'];
                    $p = '%';
                    if (strpos($w, $p) != (strlen($w) - 1)) {
                        $p = '';
                    }
                    $em .= ' width="' . intval($embed['width']) . $p . '"';
                }

                if (isset($embed['height'])) {
                    $h = $embed['height'];
                    $p = '%';
                    if (strpos($h, $p) != (strlen($h) - 1)) {
                        $p = '';
                    }
                    $em .= ' height="' . intval($embed['height']) . $p . '"';
                }

                if (isset($embed['flashvars'])) {
                    $em .= ' flashvars="' . $embed['flashvars'] . '"';
                }

                if (isset($embed['style'])) {
                    $em .= ' style="' . str_replace('javascript', '', $embed['style']) . '"';
                }
            }
            if ($em) {
                $em .= ' />';
                if ($ob) {
                    $em = $ob . $em . '</object>';
                }
            }
        }

        return $em;
    }

    public function getIP() {

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {   //check ip from share internet
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {   //to check ip is pass from proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (!empty($_SERVER['REMOTE_ADDR'])) {   //to check ip is pass from proxy
            $ip = $_SERVER['REMOTE_ADDR'];
        }else {
            $ip = $_SERVER['SERVER_ADDR'];
        }
        return $ip;
    }

    public function getHtml($k) {
        if (isset($this->params[$k])) {
            return trim(htmlentities(stripslashes($this->params[$k])));
        }
        return null;
    }

    public function clear() {
        $this->params = array();
        $this->queryParams = array();
    }

    /**  As of PHP 5.1.0  */
    public function __isset($k) {
        return isset($this->params[$k]);
    }

    /**  As of PHP 5.1.0  */
    public function __unset($k) {
        unset($this->params[$k]);
    }

    function isSe() {

        if (!isset($_SERVER['HTTP_REFERER'])) {
            return false;
        }

        $url = $_SERVER['HTTP_REFERER'];
        $pUrl = parse_url($url);
        if (!isset($pUrl['host'])) {
            return false;
        }

        if (!isset($pUrl['query'])) {
            return false;
        }

        $sArray = array();
        parse_str($pUrl['query'], $sArray);

        if (!isset($sArray['q'])) {
            if (!isset($sArray['p'])) {
                return false;
            }
        }

        return true;
    }
    
    

}

Class MethodeArgs extends HttpRequest {

    public function __construct($m, $args) {
        $m_ar = explode('::', $m);
        $method = new ReflectionMethod($m_ar[0], $m_ar[1]);
        $p = array();
        foreach ($method->getParameters() as $i => $param) {
            $name = $param->getName();
            $p[$name] = $args[$i];
        }
        //$this->params = $p;
        parent::__construct($p);
    }

}

