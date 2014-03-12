<?php

namespace Ospari;

Class Bootstrap {

    static protected $instance;

    /**
     * 
     * @return \Ospari\Bootstrap
     */
    static public function getInstance() {
        if (self::$instance == NULL) {
            self::$instance = new Bootstrap();
        }
        return self::$instance;
    }

    public function checkUserPerms($route) {
        $arr = explode('/', $route);

        $endEl = end($arr);

        $allowedPaths = array(
            'login' => TRUE,
            'reset' => TRUE,
            'install' => TRUE,
            'forgotten' => TRUE
        );
        if (isset($allowedPaths[$endEl])) {
            return TRUE;
        }

        $adminPathLength = strlen(OSPARI_ADMIN_PATH) + 1;
        if (substr($route, 0, $adminPathLength) == '/' . OSPARI_ADMIN_PATH) {

            $sess = \NZ\SessionHandler::getInstance();
            if (!$sess->getUser_id()) {
                header('location: /' . OSPARI_ADMIN_PATH . '/login?callback=' . urlencode(\NZ\Uri::getCurrent()));
                exit(1);
            }
            $sess->user_id = $sess->getUser_id();
        }
    }

    public function handleExecption(\Exception $exc) {

        if (!$this->hasDBConfig()) {
            include ( __DIR__ . '/modules/OspariAdmin/src/OspariAdmin/View/tpl/headers.php' );
            echo '<style>body{padding:0px !important;}</style><div class="col-lg-12">';
            echo '<h1>Invalid Configuration</h1>';
            echo '<p>Please open core/config/application.config.php and enter all required data</p>';
            echo '</div></body></html>';
            return;
        }

        $prevException = $exc->getPrevious();
        if (is_object($prevException) && !isset($_GET['code'])) {

            $code = $prevException->getCode();

            //42000

            if ($code == '1049') {
                //Database not found
                $confg = \NZ\Config::getInstance();
                $db_read = $confg->get('db_read');
                include ( __DIR__ . '/modules/OspariAdmin/src/OspariAdmin/View/tpl/headers.php' );
                echo '<style>body{padding:0px !important;}</style><div class="col-lg-12">';
                echo '<h1>Database(' . $db_read['database'] . ') does not exist</h1>';
                echo '<p>Please create the database and try again</p>';
                echo '</div></body></html>';
                return;
            }


            if ($code == '42S02') {
                //Table not found on database
                // redirect to install
                \header('location: ' . OSPARI_URL . '/install?code=' . $code);
                exit(1);
            }
        }

        \http_response_code(500);

        include ( __DIR__ . '/modules/OspariAdmin/src/OspariAdmin/View/tpl/headers.php' );
        echo '<style>body{padding:0px !important;}</style><div class="col-lg-12">';

        echo '<h1>' . $exc->getMessage() . '</h1>';
        echo '<hr><pre>';
        $debug = $exc->__toString();
        $debug = str_replace($_SERVER['DOCUMENT_ROOT'], '', $debug);

        $debugArr = explode('Stack trace:', $debug);
        echo $debugArr[0];

        echo '</pre>';
        if (ENV == 'dev') {
            echo '<h2>Stack trace:</h2>';
            if (isset($debugArr[1])) {
                echo '<pre>' . $debugArr[1] . '</pre>';
            }
        }

        echo '</div></body></html>';
    }

    public function getRequestURI() {
        if (!isset($_SERVER['REQUEST_URI'])) {
            throw new \Exception("\$_SERVER['REQUEST_URI'] not set");
        }

        $cwd = __DIR__;
        $dcPath = str_replace($_SERVER['DOCUMENT_ROOT'], '', $cwd);

        $uri_arr = explode('?', $_SERVER['REQUEST_URI']);

        $uri = str_replace($dcPath, '', $uri_arr[0]);


        if (substr($uri, 0, 8) == '/install') {
            unset($_COOKIE['sid']);
            setcookie("sid", "", time() - 3600);
        }

        return $uri;
    }

    public function detectOspariURL() {
        if (!isset($_SERVER['HTTP_HOST'])) {
            throw new \Exception("\$_SERVER['HTTP_HOST'] not set");
        }

//        $cwd = realpath(__DIR__ . '/..');
//        $dcPath = str_replace($_SERVER['DOCUMENT_ROOT'], '', $cwd);

        $scheme = 'http://';
        if (isset($_SERVER['HTTPS'])) {
            $scheme = 'https://';
        }

//        $ServerPort = $_SERVER['SERVER_PORT'];
//        if ($ServerPort == 80) {
//            $port = '';
//        } else {
//            $port = ':' . $ServerPort;
//        }
        return $scheme . $_SERVER['HTTP_HOST'] ;
    }

    public function hasDBConfig() {
        $confg = \NZ\Config::getInstance();
        if (!$db_read = $confg->get('db_read')) {
            return FALSE;
        }
     
        $keys = array('database',
            'username',
            'password',
            'host',
            'options');

        foreach ($keys as $k) {
            if (!isset($db_read[$k])) {
                return FALSE;
            }

            if (empty($db_read[$k])) {
                return FALSE;
            }
        }

        return TRUE;
    }

    public function isInstalled() {
        $db = \NZ\DB_Adapter::getInstance();
        $sql = "SHOW TABLES LIKE '" . OSPARI_DB_PREFIX . "_users'";
        $result = $db->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
        $r = $result->count();
        return (1 == $r);
    }

}
