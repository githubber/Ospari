<?php

namespace OspariAdmin\Controller;

use NZ\HttpRequest;
use NZ\HttpResponse;

/**
 * InstallController doesn't extend base controller, because BaseController:__construct fetches use information
 */
class InstallController {

    public function installAction(HttpRequest $req, HttpResponse $res) {
        $view = $res->getView();
        $this->setViewParts($view);
        
        $bs = \Ospari\Bootstrap::getInstance();
        if (!$bs->hasDBConfig()) {
            throw new \Exception('Invalid configuration');
            ;
        }

        if (!$this->databaseExist()) {
            throw new \Exception('Please create a database first');
            ;
        }

        if ($req->installed) {
            return $res->buildBody('install/installed.php');
        }



        $form = $this->createForm($view, $req);

        if ($req->isPOST()) {
            if ($this->validateForm($form, $req)) {
                $this->install($req);
                return $res->redirect(OSPARI_URL . '/install?installed=1');
            } else {
                $res->setViewVar('error_msg', 'Please fill out all required fields');
            }
        }

        $res->setViewVar('form', $form);


        return $res->buildBody('install/install.php');
    }

    protected function validateForm(\NZ\BootstrapForm $form, $req) {
        return $form->validate($req);
    }

    private function setViewParts(\NZ\View $view) {
        $view->head = __DIR__ . '/../View/tpl/head_mini.php';
        $view->tail = __DIR__ - '/../View/tpl/tail_mini.php';
    }

    public function databaseExist() {
        $confg = \NZ\Config::getInstance();
        $db_read = $confg->get('db_read');

        $db = \NZ\DB_Adapter::getInstance();
        $sql = "SHOW DATABASES LIKE  '" . $db_read['database'] . "'";
        $result = $db->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
        $r = $result->count();
        return (1 == $r);
    }

    protected function install($req) {
        $db = \NZ\DB_Adapter::getInstance();

        foreach ($this->getSql() as $sql) {
            $result = $db->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
        }

        $settting = new \OspariAdmin\Model\Setting();
        $settting->set('email', $req->email);
        $settting->set('title', $req->title);
        $settting->set('ospari_version', '0.1');
        $settting->save();

        $user = new \OspariAdmin\Model\User(array('email' => $req->email));
        $user->email = $req->email;
        $user->save();
        $user->changePassword($req->password);
        return TRUE;
    }

    /**
     * 
     * @param \NZ\View $view
     * @param \NZ\HttpRequest $req
     * @return \NZ\BootstrapForm
     */
    protected function createForm(\NZ\View $view, HttpRequest $req) {

        $form = new \NZ\BootstrapForm($view, $req);
        $form->setID('setting-edit-form');
        $form->setCssClass('form-horizontal');
        $form->addSubmitClass('btn btn-primary');

        $form->setSubmitValue('Install');

        $form->createElement('title')
                ->setLabelText('Blog Title')
                ->setRequired()
        ;

        $form->createElement('email')
                ->setLabelText('Email Address')
                ->setType('email')
                ->setRequired();

        $form->createElement('password')
                ->setLabelText('Password')
                ->setType('password')
                ->setRequired();


        return $form;
    }

    protected function getSql() {
        return array(
            "CREATE TABLE IF NOT EXISTS `" . OSPARI_DB_PREFIX . "drafts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(150) NOT NULL,
  `slug` char(100) NOT NULL,
  `content` text NOT NULL,
  `code` text,
  `cover` varchar(255) DEFAULT NULL,
  `thumb` varchar(255) DEFAULT NULL,
  `media_id` int(11) DEFAULT NULL,
  `tags` varchar(255) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `state` tinyint(1) NOT NULL DEFAULT '0',
  `published_at` datetime NOT NULL,
  `edited_at` datetime NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;",
            "CREATE TABLE IF NOT EXISTS `" . OSPARI_DB_PREFIX . "posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `draft_id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `content` text NOT NULL,
  `cover` varchar(255) DEFAULT NULL,
  `thumb` varchar(255) DEFAULT NULL,
  `media_id` int(11) DEFAULT NULL,
  `tags` varchar(255) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `state` tinyint(1) NOT NULL DEFAULT '0',
  `published_at` datetime NOT NULL,
  `edited_at` datetime NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB",
            "CREATE TABLE IF NOT EXISTS `" . OSPARI_DB_PREFIX . "sessions` (
  `sid` char(33) NOT NULL,
  `user_id` int(11) NOT NULL,
  `session` text NOT NULL,
  `update_date` datetime NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `sid` (`sid`)
) ENGINE=MyISAM",
            "CREATE TABLE IF NOT EXISTS `" . OSPARI_DB_PREFIX . "settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key_name` char(100) NOT NULL,
  `key_value` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key_name` (`key_name`)
) ENGINE=InnoDB ",
            "CREATE TABLE IF NOT EXISTS `" . OSPARI_DB_PREFIX . "users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` char(100) NOT NULL,
  `name` char(120) NOT NULL,
  `password` varchar(255) NOT NULL,
  `cover` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `bio` text,
  `last_login` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB",
            
            "ALTER TABLE  `" . OSPARI_DB_PREFIX . "users` ADD  `rkey` VARCHAR( 255 ) NULL AFTER  `email`"
            
        );
    }

}
