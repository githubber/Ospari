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
    public static function sendPasswordResetRequest( $from = null, OspariAdmin\Model\User $to, $subject, $body ){
        if(!$from){
            $from = new \stdClass();
            $from->email = 'it@28h.eu';
            $from->full_name = '28h Lab UG';
        }
        
        $transport = Swift_MailTransport::newInstance();

        // Create the message
        $message = Swift_Message::newInstance();
        $message->setTo(array(
           $to->email => $to->full_name
                ));
        $message->setSubject($subject);
        $message->setBody($body);
        $message->setFrom($from->email, $from->full_name);

        // Send the email
        $mailer = Swift_Mailer::newInstance($transport);
        $mailer->send($message);
    }
    
    
}
