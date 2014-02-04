<?php

namespace NZ;

abstract Class BasicAuth {

    private $realm;

    public function __construct($realm = 'Login Required') {
        $this->realm = $realm;
    }

    // check if logged in 
    public function tryLogin() {

        $realm = $this->realm;

        if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])) {
            return false;
        }

        return $this->checkLogin($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']);
    }

    // create login form
    public function requireLogin() {
        $realm = $this->realm;

        header('WWW-Authenticate: Basic realm="' . $this->realm . '"');
        header('HTTP/1.0 401 Unauthorized');

        die('Login Required');
    }

    public function getUsername() {
        if (!isset($_SERVER['PHP_AUTH_USER'])) {
            return '';
        }


        return $_SERVER['PHP_AUTH_USER'];
    }

    public function getPassword() {
        if (!isset($_SERVER['PHP_AUTH_PW'])) {
            return '';
        }


        return $_SERVER['PHP_AUTH_PW'];
    }

    abstract protected function checkLogin($username, $password);
}