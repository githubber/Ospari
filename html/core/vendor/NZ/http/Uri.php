<?php

namespace NZ;

Class Uri {

    private $uri;
    private $pUrl = array();
    private $params = array();
    private $origParams = array();
    private $hastQuery = false;

    public function __construct($uri = '') {
        if ($uri) {
            $this->uri = $uri;
            $this->pUrl = parse_url($uri);
            if (isset($this->pUrl['query'])) {
                $this->hastQuery = true;
                $output = array();
                parse_str($this->pUrl['query'], $output);
                $this->params = $output;
                $this->origParams = $output;
            }

            if (!isset($this->pUrl['host'])) {
                $this->pUrl['host'] = $uri;
            }
        } else {
            $this->build();
        }
    }

    public function build() {
        $scheme = 'http';
        if (isset($_SERVER['SERVER_PORT'])) {
            if ($_SERVER['SERVER_PORT'] == 443) {
                $scheme = 'https';
            }
        }

        $uri = $_SERVER['HTTP_HOST'];
        $this->pUrl['host'] = $uri;
        if (isset($_SERVER['REQUEST_URI'])) {
            $path = $_SERVER['REQUEST_URI'];
            $this->pUrl['path'] = $path;
        } else {
            $path = $_SERVER['PHP_SELF'];
            $this->pUrl['path'] = $path;
        }
        if (isset($_SERVER['QUERY_STRING'])) {
            $path .= '?' . $_SERVER['QUERY_STRING'];

            $this->hastQuery = true;
            $output = array();
            parse_str($_SERVER['QUERY_STRING'], $output);
            $this->params = $output;
            $this->origParams = $output;
        }

        $this->uri = $scheme . '://' . $uri . $path;
        return $scheme . '://' . $uri . $path;
    }

    public function reset() {
        $this->params = $this->origParams;
    }

    public function addParam($k, $v) {
        $this->params[$k] = $v;
    }

    public function removeParam($k) {
        unset($this->params[$k]);
    }

    public function hasParams() {
        return (count($this->params) > 0);
    }

    public function clean() {
        foreach ($this->params as $k => $v) {
            if (empty($v)) {
                unset($this->params[$k]);
            }
        }
    }

    public function __toString() {
        $sep = '';

        $arr = explode('?', $this->uri);

        $params = $this->params;


        if (!$params) {
            return $arr[0];
        }

        $qry = http_build_query($params);

        if ($qry) {
            return $arr[0] . '?' . $qry;
        }

        if ($sep) {
            return $this->uri . $sep;
        }


        return $this->uri;
    }

    public function setURL($uri) {
        $this->uri = $uri;
    }

    public function trimWWW() {
        return str_replace('www.', '', $this->uri);
    }

    public function get($p) {
        if (isset($this->params[$p])) {
            return $this->params[$p];
        }
        return null;
    }

    public function getHost() {
        if (isset($this->pUrl['host'])) {
            return $this->pUrl['host'];
        }
        return null;
    }

    // Manuel: changed, that localhost returns emtpy string
    // if not, browser does not set cookies because .localhost is no valid host
    public function getMainDomain() {
        if (isset($this->pUrl['host'])) {
            $host = $this->pUrl['host'];
            $host = str_replace('www.', '', $host);
            if ($host == 'localhost') {
                return '';
            }
            return $host;
        }
        return null;
    }

    public function getScheme() {
        if (isset($this->pUrl['scheme'])) {
            $scheme = $this->pUrl['scheme'];
            return $scheme;
        }
        return null;
    }

    public function getPath() {
        $path = '';
        if (isset($this->pUrl['path'])) {
            $path = $this->pUrl['path'];
        }

        if (!isset($this->pUrl['query'])) {
            return $path;
        }
        if ($qry = $this->pUrl['query']) {
            $path .= '?' . $qry;
        }
        return $path;
    }

    static public function getCurrent($scheme = 'http') {
        if (isset($_SERVER['HTTP_HOST'])) {
            $uri = $_SERVER['HTTP_HOST'];
        } else {
            $uri = '';
        }

        if (isset($_SERVER['REQUEST_URI'])) {
            $path = $_SERVER['REQUEST_URI'];
        } else {
            
            $path = $_SERVER['PHP_SELF'];
            
            if (isset($_SERVER['QUERY_STRING'])) {
                if ($_SERVER['QUERY_STRING']) {
                    $path .= '?' . $_SERVER['QUERY_STRING'];
                }
            }
        }

        if (isset($_SERVER['SERVER_PORT'])) {
            if ($_SERVER['SERVER_PORT'] == 443) {
                $scheme = 'https';
            }
        }

        return $scheme . '://' . $uri . $path;
    }

    static public function slugify($str, $l = 50) {
        $str = trim($str);

        $str =  str_replace(
                    array('à','á','â','ã','ä', 'ç', 'è','é','ê','ë', 'ì','í','î','ï', 'ñ', 'ò','ó','ô','õ','ö', 'ù','ú','û','ü', 'ý','ÿ', 'À','Á','Â','Ã','Ä', 'Ç', 'È','É','Ê','Ë', 'Ì','Í','Î','Ï', 'Ñ', 'Ò','Ó','Ô','Õ','Ö', 'Ù','Ú','Û','Ü', 'Ý'), 
                    array('a','a','a','a','a', 'c', 'e','e','e','e', 'i','i','i','i', 'n', 'o','o','o','o','o', 'u','u','u','u', 'y','y', 'A','A','A','A','A', 'C', 'E','E','E','E', 'I','I','I','I', 'N', 'O','O','O','O','O', 'U','U','U','U', 'Y'),
                            $str);
        
        $str = stripslashes($str);
        $str = substr($str, 0, $l);
        $str = preg_replace("/[^a-zA-z0-9ﬂ÷ˆ‹¸ƒ‰]/", "-", $str);
        $str = preg_replace("/(^(_+)|(_+)$)/", "", $str);
        $str = str_replace("ﬂ", "ss", $str);
        $str = str_replace("ƒ", "AE", $str);
        $str = str_replace("‹", "UE", $str);
        $str = str_replace("÷", "OE", $str);
        $str = str_replace("‰", "ae", $str);
        $str = str_replace("¸", "ue", $str);
        $str = str_replace("ˆ", "oe", $str);
        $str = str_replace("Ë", "e", $str);
        $str = str_replace("È", "e", $str);

        $str = str_replace("_", "-", $str);
        $str = preg_replace("/-{2,}/", "-", $str);
        $str = preg_replace("/(-$|^-)/", "", $str);
        $str = str_replace("`", "", $str);

        return strtolower($str);
    }

}
