<?php

namespace Ospari\Model;

Class Draft extends \NZ\ActiveRecord {


    public function getTableName() {
        return OSPARI_DB_PREFIX.'drafts';
    }
    
    
    
}
