<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SessionHandler
 *
 * @author 28h
 */

namespace NZ;

use NZ\DB_Trait;
use NZ\DB_Adapter;
use Zend\Db\TableGateway\TableGateway;

Class SessionHandler {

    static private $instance;
    private $obj;
    private $inited = false;
    private $uid = 0;
    private $cookieName = 'sid';

    /**
     * Konstruktor
     */
    private function __construct() {
        if (isset($_COOKIE['sid'])) {
            $this->init();
        }
        
        if (isset($_COOKIE['PHPSESSID'])) {
            $this->cookieName = 'PHPSESSID';
            $this->init('PHPSESSID');
        }
    }

    public function cache($k, $v) {
        $this->set($k, $v);
        $this->set($k . '_ctime', time());
    }

    public function getCached($id) {

        if (!$cTime = $this->get($id . '_ctime')) {
            return null;
        }

        if ($cTime < time() - 90) {
            return null;
        }

        return $this->get($id);
    }

    /**
     * Returns the instance of the session
     * @param Integer $user_id
     * @return Object $instance
     */
    static public function getInstance($user_id = 0) {
        if (self::$instance == null) {
            self::$instance = new SessionHandler();
        }
        return self::$instance;
    }

    /**
     * Returns the user linked with the session
     * @return Object User
     */
    public function mustLogin($callback = null) {

        if (!$this->getUser_id()) {
            $cbUrl = NZ_Uri::getCurrent();
            header('location: /login?callback=' . urlencode($cbUrl));
            exit(1);
        }
    }

    public function getUser() {

        if (!$user_id = $this->uid) {
            return new User();
        }

        $user = User::getInstance($user_id);


        return $user;
    }

    public function getUser_id() {
        return intval($this->uid);
    }

    public function getUserId() {
        return intval($this->uid);
    }

    public function setUser_id($user_id) {
        $this->init();
        $sid = session_id();
        if (!$sid) {
            session_start();
            $sid = session_id();
        }

        //$_SESSION['user_id'] = $user_id;
        $obj = $this->obj;

        $obj->user_id = $user_id;
        $obj->sid = session_id();

        if (isset($_SESSION['user_id'])) {
            if ($_SESSION['user_id'] == $user_id) {
                return $obj->update(array('user_id' => $user_id));
            }
        }

        if ($user_id) {

            $obj->delete(array('user_id' => $user_id));
        }
        //$obj->insert();

        $_SESSION['user_id'] = $user_id;

        $obj->save();
    }

    public function setUser($user) {

        $this->init();
        $_SESSION['user_id'] = $user->id;
        $obj = $this->obj;
        $obj->delete(array('user_id' => $user->id));
        $obj->user_id = $user->id;
        $obj->sid = session_id();
        //$obj->insert();



        $obj->save();
    }

    /**
     * Destroys the session
     */
    public function destroy() {

        session_destroy();
        setcookie("sid", "", time() - 3600, "/", $this->getCookieDomain());
    }

    // Manuel: changed \NZ\Uri() -> \NZ\Uri(COOKIE_DOMAIN)
    public function getCookieDomain() {
        $uri = new \NZ\Uri();
        $host = $uri->getMainDomain();
        if ('localhost' == $host) {
            return '';
        }

        return '.' . $uri->getMainDomain();
    }

    /**
     * Provides informations of the session by a session id
     * @param Integer $sesID Session ID
     * @return session memcache
     */
    static public function getSession($sesID) {
        //TODO
    }

    /**
     * Provides informations of the session by a user id
     * @param Integer $sesID Session ID
     * @return session memcache
     */
    static public function getSession_id($user_id) {
        //TODO
    }

    /**
     * Initializes a session
     * @param String $name session name
     */
    public function init($name = 'sid') {
        

        if ($this->inited) {
            return true;
        }
        $conf = \NZ\Config::getInstance();


        $this->inited = true;

        $this->obj = new NZ_Session_Entity();

        session_name($name);
        //session_set_cookie_params((3600*24*30), "/", COOKIE_DOMAIN );
        session_set_cookie_params(0, "/", $this->getCookieDomain());

        // Sets the Sessionhandler on methods of this class
        session_set_save_handler(array($this, '_open'), array($this, '_close'), array($this, '_read'), array($this, '_write'), array($this, '_destroy'), array($this, '_gc'));


        // start Session
        if (!isset($_SESSION)) {
            //if( PHP_SESSION_ACTIVE == session_status() ){

            session_start();
        }



        //session_write_close();
        register_shutdown_function('session_write_close');
        $this->updateLastAcess();
    }

    /**
     * Opens a Session
     * @return boolean Always returns true
     */
    function _open($path, $name) {

        return true;
    }

    /**
     * Closes a Session
     * @return boolean Always returns true
     */
    function _close() {

        //Calls the Garbage-Collector.
        $this->_gc(0);
        return true;
    }

    /**
     * Reads session-data from the database
     * @return varchar Returns the session data or an empty String
     */
    public function _read($sesID) {
        $obj = new NZ_Session_Entity($sesID);

        $this->uid = $obj->user_id;
        return $obj->session;
    }

    /**
     * Writes new Data into the Database
     * @param varchar Session id
     * @param Array Session data
     * @return boolean Is true when data is successfully written
     */
    public function _write($sesID, $data) {

        //Only write, when data is given
        if (!$data) {
            return true;
        }

        $obj = $this->obj;

        if (!isset($_SESSION['user_id'])) {
            //return true;
            $_SESSION['user_id'] = 0;
        }
        $obj->user_id = $_SESSION['user_id'];
        //$obj->update_date = $obj->now(); 
        $obj->setDateTime('update_date', new \DateTime);

        $obj->session = $data;

        try {
            return $obj->update(array('sid' => $sesID));
        } catch (\Exception $e) {
            trigger_error( $e->getMessage() );
        }

        $obj = new NZ_Session_Entity();

        if (!isset($_SESSION['user_id'])) {
            //return true;
            $_SESSION['user_id'] = 0;
        }
        $obj->user_id = $_SESSION['user_id'];
        //$obj->update_date = $obj->now(); 
        $obj->setDateTime('update_date', new \DateTime);

        $obj->session = $data;

        try {
            $obj->sid = $sesID;
            return $obj->save();
        } catch (\Exception $e) {
            throw new \Exception('faild to create session');
        }
    }

    /**
     * Deletes a session from the database
     * @param varchar Session Number
     * @return boolean Is true when session is successfully destroyed
     */
    function _destroy($sesID) {

        return $this->obj->delete(array('sid' => $sesID));
    }

    public function __set($k, $v) {
        $_SESSION[$k] = $v;
    }

    public function get($k) {
        if (isset($_SESSION[$k])) {
            return $_SESSION[$k];
        }
        return null;
    }
    
    public function setRequest(\NZ\HttpRequest $req){
        $errors = $req->getAllErrorMessages();
        $post = $req->getPostParams();
                
        foreach($errors as $k => $v){
            $post[$k.'err'] = $v;          
        }
        $this->set('postReq', $post);        
    }
    
    public function removePostRequest(){
        $this->remove('postReq');
    }
    
    public function buildRequest($req){
        $postReq = $this->get('postReq');
        
        if(!is_array($postReq)){
            return $req;
        }
        
        foreach($postReq as $k => $v){
            $req->set($k, $v);
        }
        return $req;
    }
    
    public function set($k, $v) {

        $_SESSION[$k] = $v;
    }

    public function remove($k) {
        unset($_SESSION[$k]);
    }

    public function __get($k) {
        if (isset($_SESSION[$k])) {
            return $_SESSION[$k];
        }
        return null;
    }

    public function has($k) {
        return isset($_SESSION[$k]);
    }

    /**
     * Provides a Garbage-Collector
     * Deletes all run off sessions from the database
     * @return boolean status
     */
    public function _gc($life) {
        return true;
    }

    private function updateLastAcess() {
        if ($user_id = $this->getUser_id()) {
            
        }
    }

    public function clear() {
        session_unset($_SESSION);
        unset($_SESSION);
        $this->set('tmp', '1');
        //$this->set('blup', null);        
    }

    /**
     * Closes the memcache
     */
    public function __destruct() {
        
    }

    static public function getAsNZObject($cl = array()) {
        return NZ_Record::fetchObject('sessions', $cl);
    }

}

class NZ_Session_Entity extends ActiveRecord {

    public function __construct($where = array()) {
        if (is_string($where)) {
            $where = array('sid' => $where);
        }

        parent::__construct($where);
    }

    public function getTableName() {
        return 'sessions';
    }

    public function getPrimaryKey() {
        //return 'sid';
    }

    public function save() {



        parent::save();
    }

}

/*
class NZ_Session_Entity extends TableGateway{
    use DB_Trait;
    
  
    
    
    public function __construct( $sid = NULL ) {
        $table = 'sessions';
        
        $arr = array();
        if( $sid ){
            $arr = array( 'sid' => $sid );
        }
        
        $this->subConsruct($table,  $arr );
        $this->data  = array( 'user_id' => 0, 'session' => '', 'create_date' => $this->now() );
        
    }
    
    public function __set($property, $value) {
        $this->data[$property] = $value;
    }
    
    public function save(){
        
         if( $sid = $this->sid ){
             $this->update( $this->set, array( 'sid' => $this->sid ) );
         }else{
             $this->insert( $this->data );
         }
        
    }
    
    
}
*/
