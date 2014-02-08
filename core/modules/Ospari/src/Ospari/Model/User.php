<?php

namespace Ospari\Model;
Class User extends \NZ\ActiveRecord{
    
    public function getTableName() {
        return OSPARI_DB_PREFIX.'users';
    }
    
    public function toStdObject() {
        $std = new \stdClass();
        
        //$std->name = 'Max Mustermane';
        //$std->bio = 'Short bio of Max Mustermane';
        //$std->image = '/html/upload/author.jpeg';
        
        $row = $this->row;
        
        
        unset( $row['password'] );
        
        foreach( $row as $k => $v ){
            $std->$k = $v;
        }
        
        return $std;
    }
    
}
