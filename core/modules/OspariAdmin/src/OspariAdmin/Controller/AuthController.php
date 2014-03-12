<?php


/**
 * Description of DefaultController
 *
 * @author Wahid
 */

namespace OspariAdmin\Controller;

use NZ\HttpRequest;
use NZ\HttpResponse;
use OspariAdmin\Service\SwiftMailer;
use OspariAdmin\Helper\AlertHelper;
class AuthController extends BaseController {

    /**
     * 
     * @param \NZ\HttpRequest $req
     * @param \NZ\HttpResponse $res
     * @return string
     */
    public function loginAction(HttpRequest $req, HttpResponse $res) {

        $view = $res->getView();
        $form = $this->createForm($view, $req);
        $this->setViewParts($view);
        if ($req->isPOST()&& $form->validate($req)) {
                try {
                    $user = $this->tryLogin($req->email, $req->password);
                    
                    return $res->redirect('/'.OSPARI_ADMIN_PATH);
                } catch (\Exception $ex) {
                    $view->exception = $ex;
                    $req->setErrorMessage('email', $ex->getMessage());
                }
        }
        
        $view->form = $form;
        return $res->buildBody('login.php');
        
    }
    
    public function passwordForgottenAction( HttpRequest $req, HttpResponse $res ){
        if($this->getUser()->id){
            return $res->sendErrorMessage(' You are already logged into the system');
        }
        $form = $this->createPasswordForgottenForm($res->getView(), $req);
        $this->setViewParts($res->getView());
        if($req->isPOST()&& $form->validate($req)){
                $user = \OspariAdmin\Model\User::findOne( array('email' => $req->getEmail('email')) );
                if($user){
                    $rkey = md5(microtime());
                    $user->rkey = $rkey;
                    $user->save();
                    $body = "Hallo $user->username,
Have you forgotten your password? If so then please paste the link below in to your web browser and you will be directed to a webpage where you can enter a new password.
".OSPARI_URL."/".OSPARI_ADMIN_PATH."/password/reset?rkey=".$user->rkey."
Best Regards
Ospari Team";
                    try {
                        SwiftMailer::sendPasswordResetRequest($user, 'Password Reset Request', $body);
                    } catch (\Exception $exc) {
                        return $res->sendErrorMessage(AlertHelper::getTplAsString($exc->getMessage(), $this->getLoginLink()));
                    }

                    $res->setViewVar('success', true);
                    return $res->buildBody('password_forgotten.php');
                }
                else{
                    $req->setErrorMessage('email','Invalid Email Address');
                }
        }
        $res->setViewVar('form', $form);
        return $res->buildBody('password_forgotten.php');
    }

    public function passwordResetAction( HttpRequest $req, HttpResponse $res ){
        if($this->getUser()->id){
            return $res->sendErrorMessage('You are already logged into the system');
        }
        $rkey = $req->getAlNum('rkey');
        $this->setViewParts($res->getView());
        if(!$rkey){
            return $res->sendErrorMessage(AlertHelper::getTplAsString('Invalid Password Reset key', $this->getLoginLink()));
        }
        $user = \OspariAdmin\Model\User::findOne( array('rkey' => $rkey));
        if(!$user){
            return $res->sendErrorMessage(AlertHelper::getTplAsString('Invalid Password Reset key', $this->getLoginLink()));
        }
        $form = $this->createPasswordResetForm($res->getView(), $req);
        if($req->isPOST() && $form->validate($req) ){
              if($req->get('password') == $req->get('password_confirm')){
                  $user->changePassword( $req->get('password') );
                   $res->setViewVar('success', true);
                   return $res->buildBody('password_reset.php');
              }
              $req->setErrorMessage('password', 'Password miss match!');  
        }
        $res->setViewVar('form', $form);
        
        return $res->buildBody('password_reset.php');
    }
    
    public function logoutAction( HttpRequest $req, HttpResponse $res ){
        $user = $this->getUser();
        if(!$user->id){
            $res->redirect('/'.OSPARI_ADMIN_PATH.'/login');
        }
        $this->setViewParts($res->getView());
        try {
            $sess = \NZ\SessionHandler::getInstance();
            if($sess->getUser_id()){
               $sess->destroy(); 
            }
            return $res->buildBodyFromString($res->getView()->renderSuccess(AlertHelper::getTplAsString('You have been successfully logged out of the system', $this->getLoginLink() ) ) );
        } catch (\Exception $exc) {
           return $res->sendErrorMessage($exc->getMessage());
        }

        
    }

    /**
     * 
     * @param type $email
     * @param type $password
     * @return boolean
     * @throws \Exception
     */
    protected function tryLogin($email, $password) {

        $user = \OspariAdmin\Model\User::findOne( array('email' => $email) );
        if( !$user){
            throw new \Exception( 'Invalid Email Address or Password' );
        }
        
        
        if ($user->verifyPassword($password)) {
            $sess = \NZ\SessionHandler::getInstance();
            $sess->setUser( $user );
            $user->setDateTime('last_login', new \DateTime());
            $user->save();
            
            return $user;
        }
         throw new \Exception( 'Invalid Email Address or Password' );
        
    }
    
    /**
     * 
     * @param type $view
     * @param type $req
     * @return \NZ\BootstrapForm
     */

    protected function createForm(\NZ\View $view, HttpRequest $req) {

        $form = new \NZ\BootstrapForm($view, $req);
        $form->createElement('email')
                ->setLabelText('email')
                ->setType('email')
                ->setRequired();

        $form->createElement('password')
                ->setLabelText('Password')
                ->setType('password')
                ->setRequired();
        $form->addCssClass('form-horizontal');
        $form->setSubmitValue('<i class="fa fa-sign-in"></i> Login');
        $form->addSubmitClass('btn btn-primary');
        return $form;
    }
    
    protected function createPasswordForgottenForm( \NZ\View $view, HttpRequest $req ){
        $form = new \NZ\BootstrapForm($view, $req);
        $form->createElement('email')
                ->setLabelText('email')
                ->setType('email')
                ->setRequired();
        $form->addCssClass('form-horizontal');
        $form->setSubmitValue('<i class="fa fa-envelope"></i> Send');
        $form->addSubmitClass('btn btn-primary');
        return $form;
    }
    protected function createPasswordResetForm( \NZ\View $view, HttpRequest $req ){
        $form = new \NZ\BootstrapForm($view, $req);
        $form->createElement('password')
                ->setLabelText('password')
                ->setType('password')
                ->setRequired();

        $form->createElement('password_confirm')
                ->setLabelText('Confirm Password')
                ->setType('password')
                ->setRequired();
        $form->addCssClass('form-horizontal');
        $form->setSubmitValue('Reset');
        $form->addSubmitClass('btn btn-primary');
        return $form;
    }
    private function setViewParts( \NZ\View $view ){
        $view->head = __DIR__.'/../View/tpl/head_mini.php';
        $view->tail = __DIR__-'/../View/tpl/tail_mini.php';
    }
    
    private function getLoginLink(){
        return '<a href="/'.OSPARI_ADMIN_PATH.'/login" class="alert-link">Back to Login</a>';
    }
}
