<?php

namespace OspariAdmin\Model;
Class User extends \NZ\ActiveRecord{
    
    public function getTableName() {
        return OSPARI_DB_PREFIX.'users';
    }
    
    public function verifyPassword( $password ){
        $salt = OSPARI_SALT;
        //$passwordHash = crypt($password, $salt);
        return ( crypt($password, $salt) == $this->password );
    }
    
    public function changePassword($password){
        $salt = OSPARI_SALT;
        $this->password = crypt($password, $salt);
        $this->save();
    }
    
    public function canEditDraft(Draft $draft){
        //TODO
        return true;
    }
}
