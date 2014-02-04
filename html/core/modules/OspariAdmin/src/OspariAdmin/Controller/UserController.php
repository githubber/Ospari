<?php

namespace OspariAdmin\Controller;

use NZ\HttpRequest;
use NZ\HttpResponse;

class UserController extends BaseController {

    public function editAction(HttpRequest $req, HttpResponse $res) {

        $currentUser = $this->getUser();

        $user_id = $currentUser->id;
        $user_id = 1;
        $editUser = new \OspariAdmin\Model\User($user_id);

        $view = $res->getView();
       
        if ($req->isPOST()) {
            try {
                $form = $this->createForm($view, $req);
                $editUser = $this->editUser($form, $req, $editUser);
                return $res->sendSuccessMessageJSON('User successfully edited');
            } catch (\Exception $ex) {
                return $res->sendErrorMessageJSON($ex->getMessage());
            }
        }

        $req = $editUser->toHttpRequest($req);
         $form = $this->createForm($view, $req);
        
        $res->setViewVar('form', $form);
        return $res->buildBody('user/edit.php');
    }

    private function editUser(\NZ\BootstrapForm $form, $req, $editUser) {
        if (!$form->validate($req)) {
            throw new \Exception('Please fill all required fields');
        }

        
        $salt = OSPARI_SALT;
        $password = urldecode( $req->get('password') );
        
        $passwordHash = crypt($password, $salt);
        $req->set('password', $passwordHash);
        
        $form->saveToModel($editUser);
    }
    
    

    
    /**
     * 
     * @param \NZ\View $view
     * @param \NZ\HttpRequest $req
     * @return \NZ\BootstrapForm
     */
    private function createForm(\NZ\View $view, HttpRequest $req) {

        $form = new \NZ\BootstrapForm($view, $req);
        $form->setID('user-edit-form');
        $form->setCssClass('form-horizontal');
        $form->addSubmitClass('btn btn-primary');

        $form->createElement('name')
                ->setLabelText('Name')
                ->setRequired();

        $form->createElement('cover')
                ->setLabelText('Cover Image');
        
        $form->createElement('image')
                ->setLabelText('Display Picture');
        
        $form->createElement('image')
                ->setLabelText('Display Picture');
        
        
        $form->createElement('email')
                ->setLabelText('Email Address')
                ->setType('email')
                ;

        $form->createElement('location')
                ->setLabelText('Location')
                ->setType('email')
                ;
         $form->createElement('website')
                ->setLabelText('Website')
                ->setType('email')
                ;

        $form->createElement('bio')
                ->setLabelText('Bio.')
                ->toTexArea()
                ->setRequired();

        return $form;
    }

}
