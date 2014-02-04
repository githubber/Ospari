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
}
