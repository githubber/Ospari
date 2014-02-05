<?php

namespace NZ;

Class View {

    static public $halt = false;
    //private $head;
    //private $tail;
    private $heads = array();
    private $tails = array();
    private $tailJS = array();
    private $jsArray = array();
    private $cssArray = array();
    protected $warningMessages = array();
    protected $errorMessages = array();
    protected $successMessages = array();
    protected $hintMessages = array();
    public $title;
    private $vars = array();
    public $req;
    private $encoding = 'iso-8859-1';
    private $isCached = false;
    private $cacheTime = 90;
    private $cacheID = null;
    private $doCache = false;
    private $cachedContent = null;
    protected static $instance;

    public function __construct($encoding = 'utf-8') {
        $this->encoding = $encoding;
        self::$instance = $this;
    }

    /**
     * 
     * @return \NZ\View
     */
    static public function getInstance() {
        if (self::$instance == NULL) {
            self::$instance = new View();
        }
        return self::$instance;
    }

    public function checkBox($name, $value, $attr = '') {
        return '<input type="checkbox" name="' . $name . '" value="' . $value . '" ' . $attr . '>';
        ;
    }

    public function radioBox($name, $value, $attr = '') {
        return '<input type="radio" name="' . $name . '" value="' . $value . '" ' . $attr . '>';
        ;
    }

    public function TextInput($name, $req, $attr = '') {
        if ($req->get($name . '.required') == true) {
            $attr .= ' required="true"';
        }
        $r = '<input type="text" name="' . $name . '" value="' . $this->escape($req->get($name)) . '" ' . $attr . '>';
        if ($err = $req->get($name . '.err')) {
            $r .= '<p style="color:red" class="small">' . $err . '<p>';
        }
        return $r;
    }

    public function emailInput($name, $req, $attr = '') {
        if ($req->get($name . '.required') == true) {
            $attr .= ' required="true"';
        }
        $r = '<input type="email" name="' . $name . '" value="' . $this->escape($req->get($name)) . '" ' . $attr . '>';
        if ($err = $req->get($name . '.err')) {
            $r .= '<p style="color:red" class="small">' . $err . '<p>';
        }
        return $r;
    }

    public function UrllInput($name, $req, $attr = '') {
        if ($req->get($name . '.required') == true) {
            $attr .= ' required="true"';
        }
        $r = '<input type="text" name="' . $name . '" value="' . $this->escape($req->get($name)) . '" ' . $attr . '>';
        if ($err = $req->get($name . '.err')) {
            $r .= '<p style="color:red" class="small">' . $err . '<p>';
        }
        return $r;
    }

    public function TextArea($name, $req, $attr = '') {
        $r = '<textarea name="' . $name . '" ' . $attr . '>' . $this->escape($req->get($name)) . '</textarea>';
        if ($err = $req->get($name . '.err')) {
            $r .= '<p style="color:red" class="small">' . $err . '<p>';
        }
        return $r;
    }

    public function submitInput($value = '', $attr = '') {
        if (!$attr) {
            $attr = ' class="btn"';
        }

        return '<button type="submit" ' . $attr . '>' . $value . '</button>';
    }

    public function passwordInput($name, $req, $attr = '') {
        $r = '<input type="password" name="' . $name . '" ' . $attr . '>';
        //$r .= $this->renderError( $req->get( $name.'.err') );
        return $r;
    }

    public function hiddenInput($name, $value, $attr = '') {
        return '<input type="hidden" name="' . $name . '" value="' . $this->escape($value) . '" ' . $attr . '>';
    }

    public function button($value = '', $attr = '') {
        return '<button type="button" ' . $attr . '>' . $this->escape($value) . '</button>';
    }

    public function linkButton($href, $text, $value = '') {
        
    }

    public function renderBreadCrumb($crumbs) {
        $count = 0;
        $html = '';
        foreach ($crumbs as $k => $v) {
            if ($k) {
                if ($count == 0) {
                    $html .= ' <a href="' . $k . '">' . $v . '</a> ';
                } else {
                    $html .= ' &raquo; <a href="' . $k . '">' . $v . '</a> ';
                }
            } else {
                $html .= ' &raquo; ' . $v;
            }
            $count++;
        }
        return $html . '';
    }

    public function renderUser(User $user) {
        
    }

    public function renderException($e) {
        return $this->renderError($e->getMessage());
    }

    public function renderError($str) {
        if ($str) {
            return '<div class="alert alert-danger">' . $str . '</div>';
        }
    }

    public function renderHint($str) {
        return '<div class="alert">' . $str . '</div>';
    }

    public function renderWarning($str) {
        return '<div class="alert alert-warning">' . $str . '</div>';
    }

    public function renderInfo($str) {
        return '<div class="alert alert-info">' . $str . '</div>';
    }

    public function renderSuccess($str) {
        return '<div class="alert alert-success">' . $str . '</div>';
    }

    public function setErrorMessage($msg) {
        $this->errorMessages[] = $msg;
    }

    public function setWarningMessage($msg) {
        $this->warningMessages[] = $msg;
    }

    public function setHintMessage($msg) {
        $this->hintMessages[] = $msg;
    }

    public function setSuccessMessage($msg) {
        $this->successMessages[] = $msg;
    }

    public function getErrorMessages() {
        return $this->errorMessages;
    }

    public function getWarningMessages() {
        return $this->warningMessages;
    }

    public function getHintMessages() {
        return $this->hintMessages;
    }

    public function getSuccessMessages() {
        return $this->successMessages;
    }

    public function setCaching($boolean) {
        if (isset($_COOKIE['lng'])) {
            $this->doCache = false;
            return;
        }
        $this->doCache = $boolean;
    }

    public function getFormErrorClass($k) {
        if (isset($this->formErrors[$k])) {
            return 'error';
        }
        return '';
    }

    public function getFormErrorMessage($k) {
        if (isset($this->formErrors[$k])) {
            return $this->formErrors[$k];
        }
        return '';
    }

    /*
      public function getFormValue($k){
      if(isset($this->formValues[$k])){
      return $this->formValues[$k];
      }
      return '';
      }
     */

    public function reset() {
        $this->heads = array();
        //$this->tails = array();
        $this->jsArray = array();
        $this->cssArray = array();
    }

    public function setCacheTime($seconds) {
        $this->cacheTime = $seconds;
    }

    public function setCacheFileName($name) {
        $this->cacheID = $name;
    }

    public function getCacheFileName() {
        if (!$this->cacheID) {
            $this->cacheID = md5(NZ_Uri::getCurrent());
        }
        return $this->cacheID;
    }

    public function setEncoding($encoding) {
        $this->encoding = $encoding;
    }

    public function getEncoding() {
        return $this->encoding;
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    public function getTitle($title) {
        return $this->title;
    }

    public function setTail($tpl) {
        $this->tails[$tpl] = $tpl;
    }

    public function setJS($js) {
        $this->jsArray[$js] = $js;
    }

    public function unsetJS($js = null) {
        $this->jsArray = array();
    }

    public function getJS() {
        return $this->jsArray;
    }

    public function setCSS($css) {
        $this->cssArray[$css] = $css;
    }

    public function getCSS() {
        return $this->cssArray;
    }

    public function getVars() {
        return $this->vars;
    }

    public function setHead($tpl) {
        $this->heads[$tpl] = $tpl;
    }

    public function unsetHead() {
        $this->heads = array();
    }

    public function escape($str) {
        //return htmlentities( $str, ENT_QUOTES, $this->encoding, false);
        return htmlentities($str, ENT_QUOTES, $this->encoding);
    }

    public function filter($str, $replaceChar = '*') {
        $conf = NZ_Config::getInstance();
        $langPath = $conf->langPath;
        $handle = fopen($langPath . '/badwords.txt', "r");
        while (!feof($handle)) {
            $badword = trim(fgets($handle));
            $str = eregi_replace($badword, sprintf("%'" . $replaceChar . (strlen($badword)) . "s", NULL), $str);
        }
        return $str;
    }

    public function img($src, $attr = '') {
        return '<img src="' . $src . '" ' . $attr . '>';
    }

    public function dnl2p($str) {
        return '<p>' . str_replace("\n\n", "</p><p>", $this->escape($str)) . '</p>';
    }

    public function nl2p($html) {
        
    }

    public function normalize($html) {

        $html = preg_replace('!&lt;a +href=&quot;(.*?)&quot;(?: +title=&quot;(.*?)&quot;)? *&gt;(.*?)&lt;/a&gt;!m', '<a href="$1" title="$2" rel="nofollow" target="_blank">$3</a>', $html);
        $html = preg_replace('!&lt;img +src=&quot;(.*?)&quot;(?: +title=&quot;(.*?)&quot;)? *\/?&gt;!m', '<img src="$1" title="$2" />', $html);

        /* Normalize Newlines */
        $html = str_replace("\r", "\n", $html);

        $html = str_replace("\n\n", "\n", $html);
        $html = "<p>" . str_replace("\n", "<br />", $html) . "";
        $html = "" . str_replace("<br /><br />", "</p><p>", $html) . "</p>";

        //$html = nl2br($html);
        //$linnes = explode( $html );

        return $html;
    }

    public function removeLineBreaks($string) {
        $string = str_replace("\\", "\\\\", $string);
        //$string = str_replace('/', "\\/", $string);
        $string = str_replace('"', "\\" . '"', $string);
        $string = str_replace("\b", "\\b", $string);
        $string = str_replace("\t", "\\t", $string);
        $string = str_replace("\n", "\\n", $string);
        $string = str_replace("\f", "\\f", $string);
        $string = str_replace("\r", "\\r", $string);
        $string = str_replace("\u", "\\u", $string);
        return $string;
    }

    private function makeClickable($url) {
        $url = str_replace("\\r", "\r", $url);
        $url = str_replace("\\n", "\n<BR>", $url);
        $url = str_replace("\\n\\r", "\n\r", $url);

        $in = array(
            '`((?:https?|ftp)://\S+[[:alnum:]]/?)`si',
            '`((?<!//)(www\.\S+[[:alnum:]]/?))`si'
        );
        $out = array(
            '<a href="$1"  rel=nofollow>$1</a> ',
            '<a href="http://$1" rel=\'nofollow\'>$1</a>'
        );
        return preg_replace($in, $out, $url);
    }

    public function toLiteHTML($html, $escape = true, $normalize = true) {
        //$html = preg_replace( "/\nhttp:\/\/+(\S+)/", "\n<a href=\"http://$1\" rel=\"nofollow\" target=\"_blank\">http://$1</a>", $html);
        //$html = preg_replace( "/\shttp:\/\/+(\S+)/", " <a href=\"http://$1\" rel=\"nofollow\" target=\"_blank\">http://$1</a>", $html);

        $html = preg_replace("/\nhttp:\/\/+(\S+)/", "\n<a href=\"http://$1\">http://$1</a>", $html);
        $html = preg_replace("/\shttp:\/\/+(\S+)/", " <a href=\"http://$1\">http://$1</a>", $html);


        /* Escaped (Safe) by Default */
        if ($escape) {
            $html = $this->escape($html);
        }

        if ($normalize) {
            $html = $this->normalize($html);
        }
        // NEU
        //$html = preg_replace('/(<a href=")(.*?)(">)(.*?)(<\\/a>)/is', "<a href=\"http://$2\" target=\"_blank\">$4</a>", $html);
        //$html = preg_replace( "/href\=\"(.*)\"/s", "href=\"$1\" rel=\"nofollow\" target=\"_blank\"", $html );

        return $html;




        //$html = preg_replace( "/\shttp:\/\/+(\S+)/", "<a href=\"http://$1\">http://$1</a>", $html);
        $html = preg_replace("/href\=\"(.*)\"/", "href=\"$1\" rel=\"nofollow\" target=\"_blank\"", $html);

        //$html = preg_replace("#(^|[> ])([\w]+?://[\w\#$%&~/.\-;:=,?@\[\]+]*)#is", "\\1<a href=\"\\2\" target=\"_blank\">\\2</a>", $html);
        //$html = preg_replace("#(^|[> ])((www|ftp)\.[\w\#$%&~/.\-;:=,?@\[\]+]*)#is", "\\1<a href=\"http://\\2\" target=\"_blank\">\\2</a>", $html);

        return $html;
        // END NEU

        /* ALT
          $p[] = '"(( |^)((ftp|http|https){1}://)[-a-zA-Z0-9@:%_+.~#?&//=]+)"i';
          $r[] = '<a href="$1" target="_blank">\1</a>';
          $p[] = '"( |^)(www.[-a-zA-Z0-9@:%_+.~#?&//=]+)"i';
          $r[] = '\1<a href="http://$2" target="_blank">\\2</a>';
          //$p[] = '"([_.0-9a-z-]+@([0-9a-z][0-9a-z-]+.)+[a-z]{2,3})"i';
          //$r[] = '<a href="mailto:\$1">\\1</a>';
          //$p[] ='":-)"';
          //$r[] = ' <em class="smilie> </em> '

          $html = preg_replace($p, $r, $html);

          return $html;
          END ALT */
    }

    public function htmlize($html, $escape = true, $normalize = true) {

        /* Escaped (Safe) by Default */
        if ($escape) {
            $html = $this->escape($html);
        }

        if ($normalize) {
            $html = $this->normalize($html);
        }

        $html = str_replace(' www.', ' http://www.', $html);

        $replacement = "$1<a class=\"extern\" target=\"_blank\" href=\"$2$3\">$2$3</a>";
        //$html = preg_replace( "#([^\"=>]|(?:r />))(http://)([^\s<>,]+(?=\.\s)|[^\s<>,]+)([\s\n<>,\.])#sm" , $replacement , $html );
        $html = preg_replace("#([^\"=>]|(?:r />))(http://)([^\s<>,]+(?=\.\s)|[^\s<>,]+)([\s\n<>,\.])#sm", $replacement, $html);
        return $html;
    }

    public function renderPart($tpl) {
        include( $tpl );
    }

    public function getPartialContent($tpl) {
        ob_start();
        $this->renderPart($tpl);
        $cont = ob_get_contents();
        ob_end_clean();

        return $cont;
    }
    
    public function getContent($tpl) {
        ob_start();
        $this->render($tpl);
        $cont = ob_get_contents();
        ob_end_clean();

        return $cont;
    }

    public function getMainContent($tpl) {
        ob_start();
        $this->render($tpl);
        $cont = ob_get_contents();
        ob_end_clean();

        return $cont;
    }

    public function isCached() {
        if (!$this->doCache) {
            return false;
        }

        $cache_conf = array(
            "cache_dir" => "/tmp/",
            "life_time" => $this->cacheTime,
            "do_caching" => true,
        );

        $cache = new NZ_Cache($cache_conf);

        $id = $this->getCacheFileName();

        if ($cont = $cache->get_string($id)) {
            $this->cachedContent = $cont;
            return true;
        }
        $this->cachedContent = null;
        return false;
    }

    public function render($tpl) {

        foreach ($this->heads as $head) {
            include( $head );
        }

        if ($this->doCache) {

            $cache_conf = array(
                "cache_dir" => "/tmp/",
                "life_time" => $this->cacheTime - 3,
                "do_caching" => true,
            );

            $cache = new NZ_Cache($cache_conf);

            $id = $this->getCacheFileName();

            //if( !$cont = $cache->get_string( $id ) ){
            if (!$cont = $this->cachedContent) {

                ob_start();
                include( $tpl );
                $cont = ob_get_contents();
                ob_end_clean();
                $cache->cache_string($id, $cont);
            }

            echo $cont;
        } else {
            include( $tpl );
        }

        foreach ($this->tails as $tail) {
            include( $tail );
        }
    }

    public function options($opts, $sel = null) {

        $r = '';
        foreach ($opts as $k => $v) {


            if ($k == $sel) {
                $r .= '<option value="' . $k . '" selected="selected">' . $this->escape($v) . '</option>';
            } else {
                $r .= '<option value="' . $k . '">' . $this->escape($v) . '</option>';
            }
        }
        return $r;
    }

    public function optionsExt($opts, $sel = null) {

        $r = '';
        foreach ($opts as $k => $data) {

            if (isset($data["onClick"]))
                $oncl = ' onclick="' . $data["onClick"] . '" ';

            if ($k == $sel) {
                $r .= '<option ' . $oncl . ' value="' . $k . '" selected="selected">' . $this->escape($data["v"]) . '</option>';
            } else {
                $r .= '<option ' . $oncl . ' value="' . $k . '">' . $this->escape($data["v"]) . '</option>';
            }
        }
        return $r;
    }

    public function renderTailJS() {
        foreach ($this->tailJS as $js) {
            echo '<script src="' . $js . '" type="text/javascript"></script>';
        }
    }

    public function renderCheckboxes($ckbs) {
        
    }

    public function checkboxes($name, $otps, $sel, $attr = '') {
        $r = array();
        $selKeys = array_flip((array) $sel);
        foreach ($otps as $k => $v) {

            if (isset($selKeys[$k])) {
                $r[$v] = ' <input ' . $attr . ' name="' . $name . '" type="checkbox" checked="checked" value="' . $k . '">';
            } else {
                $r[$v] = ' <input ' . $attr . ' name="' . $name . '" type="checkbox" value="' . $k . '">';
            }
        }
        return $r;
    }

    public function radioBoxes($name, $otps, $sel, $attr) {
        $r = array();

        foreach ($otps as $k => $v) {

            if ($k == $sel) {
                $r[$v] = ' <input ' . $attr . ' name="' . $name . '" type="radio" checked="checked" value="' . $k . '">';
            } else {
                $r[$v] = ' <input ' . $attr . ' name="' . $name . '" type="radio" value="' . $k . '">';
            }
        }
        return $r;
    }

    public function encrypt_mail($email) {
        $to_encode = '<a href="mailto:' . $email . '">' . $email . '</a>';
        return $this->encrypt_html($to_encode);
    }

    public function renderTag($tag, $value) {
        return '<' . $tag . '>' . $value . '</' . $tag . '>';
    }

    public function encrypt_html($tag) {
        $encrypted = '';
        for ($i = 0; $i < strlen($tag); $i++) {
            $encrypted .= '%' . dechex(ord(substr($tag, $i, 1)));
        }

        $encrypted = "<script type=\"text/javascript\">/* <![CDATA[ */ document.write(unescape('$encrypted')); /* ]]> */</script>";

        return $encrypted;
    }

    public function __set($k, $v) {
        $this->vars[$k] = $v;
    }

    public function __get($k) {
        if (isset($this->vars[$k])) {
            return $this->vars[$k];
        }
        return null;
    }

    public function get($k) {
        if (isset($this->vars[$k])) {
            $v = $this->vars[$k];
            if (is_string($v)) {
                return $this->escape($v);
            }
            return $v;
        }
        return null;
    }

    public function renderContent($content) {
        ob_start();

         if( $head = $this->head ){
            include ( $head );
        }
        echo $content;
       
         if( $tail = $this->tail ){
            include ( $tail );
        }

        $content = ob_get_clean();

        return $content;
    }
    public function renderReadableNumber( $num, $lang ){
        if($num <1000){
            return $num;
        }
        if( $num<100000 ){
            return round(($num/1000),1).' '.$lang->thousand_short;
        }
        
        if( $num <1000000 ){
            return (round(($num/100000),1)*100).' '.$lang->thousand_short;
        }
        return round(($num/1000000),1).' '.$lang->million_short;
    }

}