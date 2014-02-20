<?php

namespace OspariAdmin\Model;

Class Media extends \NZ\ActiveRecord{
    
    public function getTableName() {
        return OSPARI_DB_PREFIX.'medias';
    }
}