<?php


/**
 * Description of DefaultController
 *
 * @author Wahid
 */

namespace OspariAdmin\Controller;

use NZ\HttpRequest;
use NZ\HttpResponse;

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

        if ($req->isPOST()) {
            if ($form->validate($req)) {
                try {
                    $this->tryLogin($req->email, $req->password);
                    return $res->redirect('/admin');
                } catch (\Exception $ex) {
                    $view->exception = $ex;
                }
            }
        }
        
        $view->form = $form;
        return $res->buildBody('login.php');
        
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
        if( !$user->id ){
            /**
             * For security reasone we don not give the exact informatiom
             */
            throw new \Exception( 'Login failed' );
        }
        
        
        if (password_verify( $password, $user->password)) {
            return TRUE;
        }
         throw new \Exception( 'Login failed' );
        
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
                ->setRequired();

        $form->createElement('password')
                ->setLabelText('Password')
                ->setRequired();

        return $form;
    }

}
