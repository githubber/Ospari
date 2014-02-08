<?php

namespace OspariAdmin\Model;

Class Media extends \NZ\ActiveRecord{
    
    public function getTableName() {
         // not in use yet
        return '';
        //return OSPARI_DB_PREFIX.'media';
    }
}