<?php

namespace NZ;

class Mailer {

    protected $conf;

    public function __construct($conf) {
        $this->conf = $conf;
    }

    public function send($to, $subject, $body, $alt = '', $attchArray = null, $fromData = array() ) {
        $config = $this->conf;

        $h = $body;

        $smtpServer = $config->mail_smtp; //'smtp.ranksider.com';
        $username = $config->mail_username; //'noreply@smtp.ranksider.com';
        $password = $config->mail_password; //'pass';
        
        
        
        if( $fromData ){
            $from = $fromData[0];
            $name = $fromData[1];
        }else{
            $from = $config->mail_from;
            $name = $config->mail_name;
        }
        
        
        $config = array(
            'ssl' => 'tls',
            'auth' => 'login',
            'username' => $username,
            'password' => $password);


        require_once(__DIR__ . '/../vendor/Swift/lib/swift_required.php');

        $transport = \Swift_SmtpTransport::newInstance($smtpServer, 25, 'tls')
                ->setUsername($username)
                ->setPassword($password);

        $mailer = \Swift_Mailer::newInstance($transport);

        $message = \Swift_Message::newInstance()

        // Give the message a subject
        ->setSubject( $subject )

        // Set the From address with an associative array
        ->setFrom(array( $from =>  $name ))

        // Set the To addresses with an associative array
        ->setTo(array( $to ))

        // Give it a body
        ->setBody( $body )
        
        ->setContentType('text/html');
        
        if (is_array($attchArray)) {
            foreach ($attchArray as $k => $v) {
               $message->attach(\Swift_Attachment::fromPath( $v )->setFilename( $k ) );
            }
        }
        
        return $mailer->send( $message );
        
        return;

        // $transport = new Zend_Mail_Transport_Smtp($smtpServer, $config);
        $transport = new \Zend\Mail\Transport\Smtp();

        $options = new \Zend\Mail\Transport\SmtpOptions(array(
            'name' => $name,
            'host' => $smtpServer,
            'connection_class' => 'plain',
            'connection_config' => array(
                'ssl' => 'tls',
                'username' => $username,
                'password' => $password,
            ),
                ));

        $transport->setOptions($options);

        $html = new \Zend\Mime\Part($body);
        $html->type = "text/html";


        $parts = array($html);

        if (is_array($attchArray)) {
            foreach ($attchArray as $k => $v) {
                $att = new \Zend\Mime\Part($v);
                $att->filename = $k;
                $att->type = 'application/pdf';
                //$att->encoding = \Zend\Mime\;
                //$att->filename
                $parts[] = $att;
            }
        }



        //$text = new \Zend\Mime\Part(strip_tags($body));
        //$text->type = "text/plain";

        $message = new \Zend\Mime\Message();
        $message->setParts($parts);

        $mail = new \Zend\Mail\Message('UTF-8');

        $mail->setFrom($from);


        $mail->addTo($to);

        $mail->setSubject($subject);
        $mail->setBody($message);
        $transport->send($mail);
    }

}