<?php

namespace OspariAdmin\Model;

Class Post extends \NZ\ActiveRecord{
    
    const STATE_PUBLISHED = 1;
    
    public function getTableName() {
        return OSPARI_DB_PREFIX.'posts';
    }
    
    public function getUrl(){
        return OSPARI_URL.'/post/'.$this->slug;
    }
    
    public function isPublished(){
        return ($this->state == self::STATE_PUBLISHED);
    }
}

