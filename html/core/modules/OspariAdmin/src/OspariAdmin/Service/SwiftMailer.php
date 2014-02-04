<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace OspariAdmin\Service;
require_once $_SERVER['DOCUMENT_ROOT'].'/core/vendor/Swift/swift_required.php';
/**
 * Description of SwiftMailer
 *
 * @author fon-pah
 */
class SwiftMailer {
    public static function sendPasswordResetRequest( $from, OspariAdmin\Model\User $to, $subject, $body ){
        if(!$from){
            $from = new \stdClass();
            $from->email = 'it@28h.eu';
            $from->full_name = '28h Lab UG';
        }
        $view = \NZ\View::getInstance();
        $view->body = $body;
        $view->subject =$subject;
        $content = $view->getContent(__DIR__.'/../View/tpl/mail/password_reset_request.php');
        $title = 'Password Reset Request';
        self::send($from, $to, $title, $content);
    }
    
    
    public static function send( $from, OspariAdmin\Model\User $to, $title, $body){
        $transport = Swift_MailTransport::newInstance();

        // Create the message
        $message = Swift_Message::newInstance();
        $message->setTo(array(
           $to->email => $to->full_name
                ));
        $message->setSubject($title);
        $message->setBody($body);
        $message->setFrom($from->email, $from->full_name);

        // Send the email
        $mailer = Swift_Mailer::newInstance($transport);
        $mailer->send($message);
    }
}
